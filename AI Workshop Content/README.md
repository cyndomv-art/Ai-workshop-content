# AI Workshop Post-Survey System

A complete post-workshop survey system for the "AI Workshop: Building Confidence & Capability" featuring professional styling, comprehensive data collection, and automated marketing asset generation.

## Files Overview

- `survey.html` - Main survey form with 7 sections
- `thankyou.html` - Personalized confirmation page with workshop prompts
- `admin.html` - Real-time dashboard for viewing responses and analytics
- `save.php` - Server-side script for processing submissions and generating exports
- `responses.json` - All survey responses
- `testimonials.json` - Public-approved testimonials
- `leads.json` - High-priority leads (NPS 9-10 or interested in paid services)
- `early_adopters.json` - Participants who built and tested their GPT
- `best_quotes.html` - Marketing-ready testimonial page organized by theme

## Deployment Instructions

### Option 1: Local Development (Recommended for Testing)

1. **Set up a local PHP server:**
   ```bash
   # Using PHP built-in server
   cd /path/to/your/project
   php -S localhost:8000
   ```

2. **Access the survey:**
   - Survey: `http://localhost:8000/survey.html`
   - Admin Dashboard: `http://localhost:8000/admin.html`

### Option 2: Web Hosting

1. **Upload all files** to your web server
2. **Ensure PHP support** (PHP 7.0+ required)
3. **Set file permissions:**
   ```bash
   chmod 755 save.php
   chmod 666 *.json  # Allow PHP to write to JSON files
   ```

4. **Access URLs:**
   - Survey: `https://yourdomain.com/survey.html`
   - Admin Dashboard: `https://yourdomain.com/admin.html`

### Option 3: GitHub Pages (Static Version)

For a static version without PHP processing:

1. Remove the PHP submission logic from `survey.html`
2. Use a service like Formspree or Netlify Forms for form handling
3. Host on GitHub Pages

## Data Storage

The system stores data in JSON files:
- **responses.json**: Complete survey responses
- **testimonials.json**: Public testimonials with NPS scores
- **leads.json**: Qualified leads for follow-up
- **early_adopters.json**: Quick wins for case studies

## Key Features

### Survey Sections
1. **Basic Information** - Contact details
2. **Workshop Impact** - 5 skills assessment and confidence tracking
3. **Hands-On Building** - GPT creation success and testing
4. **Career Impact** - Pathway exploration and challenges
5. **Testimonial Collection** - NPS and pull quotes
6. **Future Learning** - Additional interests
7. **Permission** - Public sharing and contact preferences

### Automated Processing
- **Confidence Gain Calculation**: Before/after score difference
- **High-Impact Testimonials**: NPS 9-10 + public permission + 4+ point gain
- **Lead Qualification**: NPS 9-10 or paid service interest
- **Early Adopter Identification**: Successful GPT builders who test immediately

### Marketing Assets
- **Best Quotes Page**: Organized testimonials by theme
- **Lead Segmentation**: Ready-to-contact qualified prospects
- **Success Metrics**: Real-time dashboard with key KPIs

## Security Notes

- No collection of classified, PII, CUI, or sensitive information
- All examples are hypothetical and unclassified
- Data stored locally in JSON format
- No external API dependencies

## Customization

### Styling
- Navy blue: `#1B3B6F`
- Gold accent: `#C19A6B`
- Mobile-responsive design

### Branding
- Cynthia Vazquez branding in header
- Lead with Impact links throughout

### Form Validation
- Required fields enforced
- Email format validation
- Conditional field display

## Troubleshooting

### Common Issues

**"Submission failed" error:**
- Ensure `save.php` is executable
- Check file permissions on JSON files
- Verify PHP is enabled on server

**Data not saving:**
- Check write permissions on directory
- Ensure JSON files are not corrupted

**Styling issues:**
- Check CSS custom properties support
- Verify font loading (Inter font family)

### Manual Data Export

If PHP processing fails, responses can be manually exported by:
1. Opening browser developer tools
2. Checking network tab for form submission
3. Copying the JSON payload
4. Manually adding to `responses.json`

## Support

For issues or customization requests, contact the development team.

---

**Workshop Details:** AI Workshop: Building Confidence & Capability  
**Date:** Monday, January 19th, 2026  
**Format:** 90-minute virtual event  
**Audience:** Federal/government professionals</content>
<parameter name="filePath">c:\Users\cynva\Desktop\claude-code-practice\AI Workshop Content\README.md