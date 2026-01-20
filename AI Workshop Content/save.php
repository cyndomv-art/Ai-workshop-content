<?php
// save.php
// Accepts JSON POST and appends to responses.json, updates testimonials.json, leads.json, early_adopters.json, and best_quotes.html

header('Content-Type: application/json');

$raw = trim(file_get_contents('php://input'));
if(!$raw){ http_response_code(400); echo json_encode(['error'=>'No input']); exit; }

$data = json_decode($raw, true);
if(!$data){ http_response_code(400); echo json_encode(['error'=>'Invalid JSON']); exit; }

$required = ['fullName','role','organization','email','biggestImpact','implementFirst','firstAction','builtGPT','pullQuote','valuablePart','recommend'];
foreach($required as $r){ if(empty($data[$r])){ http_response_code(400); echo json_encode(['error'=>'Missing field: '.$r]); exit; } }

$now = date('c');
$data['submittedAt'] = $data['submittedAt'] ?? $now;

$base = __DIR__ . DIRECTORY_SEPARATOR;
$responsesFile = $base . 'responses.json';
$testimonialsFile = $base . 'testimonials.json';
$leadsFile = $base . 'leads.json';
$earlyAdoptersFile = $base . 'early_adopters.json';
$bestQuotesFile = $base . 'best_quotes.html';

// ensure files exist
if(!file_exists($responsesFile)) file_put_contents($responsesFile, json_encode([], JSON_PRETTY_PRINT));
if(!file_exists($testimonialsFile)) file_put_contents($testimonialsFile, json_encode([], JSON_PRETTY_PRINT));
if(!file_exists($leadsFile)) file_put_contents($leadsFile, json_encode([], JSON_PRETTY_PRINT));
if(!file_exists($earlyAdoptersFile)) file_put_contents($earlyAdoptersFile, json_encode([], JSON_PRETTY_PRINT));

// safely append response to responses.json
$lock = fopen($responsesFile, 'c+');
if($lock){
  if(flock($lock, LOCK_EX)){
    $contents = stream_get_contents($lock);
    $arr = [];
    if($contents) $arr = json_decode($contents, true) ?: [];
    $arr[] = $data;
    ftruncate($lock, 0);
    rewind($lock);
    fwrite($lock, json_encode($arr, JSON_PRETTY_PRINT));
    fflush($lock);
    flock($lock, LOCK_UN);
  }
  fclose($lock);
} else {
  // fallback
  $arr = json_decode(file_get_contents($responsesFile), true) ?: [];
  $arr[] = $data;
  file_put_contents($responsesFile, json_encode($arr, JSON_PRETTY_PRINT));
}

// Update testimonials.json (public permission only)
$testimonials = json_decode(file_get_contents($testimonialsFile), true) ?: [];
if(!empty($data['sharePublic']) && ($data['sharePublic'] === true || $data['sharePublic'] === 'on' || $data['sharePublic'] === '1')){
  $nps = isset($data['nps'])? intval($data['nps']):0;
  $confGain = isset($data['confGain'])? intval($data['confGain']):0;
  $testimonial = [
    'name' => ($data['publicName']?:$data['fullName']),
    'title' => ($data['role']?:''),
    'quote' => $data['pullQuote'] ?? '',
    'nps' => $nps,
    'confGain' => $confGain,
    'submittedAt' => $data['submittedAt'],
    'highImpact' => ($nps >= 9 && $confGain >= 4) ? true : false
  ];
  $testimonials[] = $testimonial;
  file_put_contents($testimonialsFile, json_encode($testimonials, JSON_PRETTY_PRINT));
}

// Update leads.json: high-priority if NPS 9-10 or interest in paid services
$leads = json_decode(file_get_contents($leadsFile), true) ?: [];
$npsVal = isset($data['nps'])? intval($data['nps']):0;
$paidServices = ['1-on-1 AI Strategy Coaching','Custom Team Workshop for My Organization','AI Implementation Consulting'];
$interest = [];
if(!empty($data['interest']) && is_array($data['interest'])) $interest = $data['interest'];

$isLead = false;
if($npsVal >= 9) $isLead = true;
foreach($interest as $it){ if(in_array($it, $paidServices)) $isLead = true; }

if($isLead){
  $lead = [
    'name' => $data['fullName'] ?? '',
    'email' => $data['email'] ?? '',
    'title' => $data['role'] ?? '',
    'interest' => $interest,
    'nps' => $npsVal,
    'notes' => ($data['firstAction'] ?? ''),
    'submittedAt' => $data['submittedAt']
  ];
  $leads[] = $lead;
  file_put_contents($leadsFile, json_encode($leads, JSON_PRETTY_PRINT));
}

// Update early_adopters.json: people who built GPT successfully and already tested or plan to use this week
$earlyAdopters = json_decode(file_get_contents($earlyAdoptersFile), true) ?: [];
$builtGPT = $data['builtGPT'] ?? '';
$testedGPT = $data['testedGPT'] ?? '';
if($builtGPT === 'Yes' && ($testedGPT === 'Yes, already used it' || $testedGPT === 'Plan to use it this week')){
  $adopter = [
    'name' => $data['fullName'] ?? '',
    'email' => $data['email'] ?? '',
    'title' => $data['role'] ?? '',
    'builtGPT' => $builtGPT,
    'testedGPT' => $testedGPT,
    'gptUse' => $data['gptUse'] ?? '',
    'submittedAt' => $data['submittedAt']
  ];
  $earlyAdopters[] = $adopter;
  file_put_contents($earlyAdoptersFile, json_encode($earlyAdopters, JSON_PRETTY_PRINT));
}

// Generate / update best_quotes.html
$allTestimonials = json_decode(file_get_contents($testimonialsFile), true) ?: [];
$impactQuotes = [];
$easeQuotes = [];
$careerQuotes = [];
$federalQuotes = [];

foreach($allTestimonials as $t){
  $quote = htmlspecialchars($t['quote'] ?? '');
  $name = htmlspecialchars($t['name'] ?? '');
  $title = htmlspecialchars($t['title'] ?? '');
  $nps = intval($t['nps'] ?? 0);
  if($nps >= 9){
    $formatted = "<blockquote>\"$quote\"<br><cite>$name, $title</cite></blockquote>";
    // Categorize by theme (simple keyword matching)
    $lowerQuote = strtolower($quote);
    if(strpos($lowerQuote, 'impact') !== false || strpos($lowerQuote, 'confidence') !== false || strpos($lowerQuote, 'build') !== false){
      $impactQuotes[] = $formatted;
    } elseif(strpos($lowerQuote, 'easy') !== false || strpos($lowerQuote, 'simple') !== false || strpos($lowerQuote, 'quick') !== false){
      $easeQuotes[] = $formatted;
    } elseif(strpos($lowerQuote, 'career') !== false || strpos($lowerQuote, 'job') !== false || strpos($lowerQuote, 'role') !== false){
      $careerQuotes[] = $formatted;
    } elseif(strpos($lowerQuote, 'federal') !== false || strpos($lowerQuote, 'government') !== false || strpos($lowerQuote, 'policy') !== false){
      $federalQuotes[] = $formatted;
    } else {
      $impactQuotes[] = $formatted; // default
    }
  }
}

$html = "<!doctype html>
<html lang='en'>
<head>
  <meta charset='utf-8' />
  <meta name='viewport' content='width=device-width,initial-scale=1' />
  <title>Marketing Gold - Best Quotes</title>
  <style>
    body{font-family:Arial,sans-serif;margin:20px;color:#333}
    h1{color:#1B3B6F}
    h2{color:#C19A6B}
    blockquote{margin:10px 0;padding:10px;border-left:4px solid #C19A6B;background:#f9f9f9}
    cite{font-style:italic;color:#666}
  </style>
</head>
<body>
  <h1>Marketing Gold: Best Testimonials</h1>
  <p>Organized by theme for easy copy-paste into marketing materials.</p>
  
  <h2>Impact & Confidence Building</h2>
  " . implode('', $impactQuotes) . "
  
  <h2>Ease of Use</h2>
  " . implode('', $easeQuotes) . "
  
  <h2>Career Value</h2>
  " . implode('', $careerQuotes) . "
  
  <h2>Federal/Government Relevance</h2>
  " . implode('', $federalQuotes) . "
</body>
</html>";

file_put_contents($bestQuotesFile, $html);

echo json_encode(['status'=>'ok']);
exit;
?>
