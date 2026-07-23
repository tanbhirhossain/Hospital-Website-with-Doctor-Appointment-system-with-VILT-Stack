<?php

namespace Modules\EMAILMARKETING\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Modules\EMAILMARKETING\Models\EmailCampaign;
use Modules\EMAILMARKETING\Models\EmailCampaignRecipient;
use Modules\EMAILMARKETING\Models\EmailTemplate;

class EmailTemplateRenderer
{
    /**
     * @return array{subject: string, preheader: string, html: string, text: string}
     */
    public function renderCampaign(EmailCampaign $campaign, ?EmailCampaignRecipient $recipient = null): array
    {
        $snapshot = $campaign->template_snapshot ?: [];
        $template = $campaign->relationLoaded('template') ? $campaign->template : $campaign->template()->first();

        $subject = $campaign->subject ?: (string) data_get($snapshot, 'subject', $template?->subject ?? 'Hospital update');
        $preheader = $campaign->preheader ?: (string) data_get($snapshot, 'preheader', $template?->preheader ?? '');
        $builder = data_get($snapshot, 'builder_json', $template?->builder_json ?? []);
        $htmlContent = (string) data_get($snapshot, 'html_content', $template?->html_content ?? '');
        $textContent = (string) data_get($snapshot, 'text_content', $template?->text_content ?? '');

        $variables = $this->variables($campaign, $recipient);
        $subject = $this->replaceVariables($subject, $variables);
        $preheader = $this->replaceVariables($preheader, $variables);

        if ($htmlContent !== '') {
            $body = $this->replaceVariables($htmlContent, $variables);
        } else {
            $body = $this->renderBlocks($this->normalizeBlocks($builder), $variables, $recipient);
        }

        $html = $this->wrapHtml($subject, $preheader, $body, $variables, $recipient);
        $text = $textContent !== ''
            ? $this->replaceVariables($textContent, $variables)
            : trim(strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], ["\n", "\n", "\n", "\n\n"], $html)));

        return compact('subject', 'preheader', 'html', 'text');
    }

    /**
     * @return array{subject: string, preheader: string, html: string, text: string}
     */
    public function renderTemplate(EmailTemplate $template, ?string $testEmail = null, ?string $testName = null): array
    {
        $campaign = new EmailCampaign([
            'name' => 'Template preview',
            'subject' => $template->subject,
            'preheader' => $template->preheader,
            'template_snapshot' => [
                'subject' => $template->subject,
                'preheader' => $template->preheader,
                'builder_json' => $template->builder_json,
                'html_content' => $template->html_content,
                'text_content' => $template->text_content,
            ],
        ]);

        $recipient = null;
        if ($testEmail) {
            $recipient = new EmailCampaignRecipient([
                'name' => $testName ?: 'Preview Subscriber',
                'email' => $testEmail,
                'tracking_token' => 'preview',
            ]);
        }

        return $this->renderCampaign($campaign, $recipient);
    }

    /** @param mixed $blocks @return array<int, array<string, mixed>> */
    public function normalizeBlocks(mixed $blocks): array
    {
        if (is_string($blocks)) {
            $decoded = json_decode($blocks, true);
            $blocks = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        if (! is_array($blocks)) {
            return [];
        }

        return collect($blocks)
            ->filter(fn ($block): bool => is_array($block) && isset($block['type']))
            ->values()
            ->all();
    }

    /** @param array<int, array<string, mixed>> $blocks @param array<string, string> $variables */
    private function renderBlocks(array $blocks, array $variables, ?EmailCampaignRecipient $recipient = null): string
    {
        if ($blocks === []) {
            $blocks = $this->defaultBlocks();
        }

        return collect($blocks)
            ->map(fn (array $block): string => $this->renderBlock($block, $variables, $recipient))
            ->implode('');
    }

    /** @param array<string, mixed> $block @param array<string, string> $variables */
    private function renderBlock(array $block, array $variables, ?EmailCampaignRecipient $recipient = null): string
    {
        $type = (string) ($block['type'] ?? 'text');
        $data = (array) ($block['data'] ?? []);

        return match ($type) {
            'hero' => $this->heroBlock($data, $variables, $recipient),
            'text' => $this->textBlock($data, $variables),
            'image' => $this->imageBlock($data, $variables),
            'button' => $this->buttonBlock($data, $variables, $recipient),
            'divider' => '<tr><td style="padding: 8px 32px;"><hr style="border:0;border-top:1px solid #e2e8f0;margin:0;"></td></tr>',
            'tips' => $this->tipsBlock($data, $variables),
            'cta' => $this->ctaBlock($data, $variables, $recipient),
            'footer' => $this->footerBlock($data, $variables),
            default => $this->textBlock($data, $variables),
        };
    }

    /** @param array<string, mixed> $data @param array<string, string> $variables */
    private function heroBlock(array $data, array $variables, ?EmailCampaignRecipient $recipient): string
    {
        $eyebrow = $this->escape($this->replaceVariables((string) Arr::get($data, 'eyebrow', 'Health update'), $variables));
        $title = $this->escape($this->replaceVariables((string) Arr::get($data, 'title', 'Your health matters'), $variables));
        $subtitle = $this->lineBreaks($this->replaceVariables((string) Arr::get($data, 'subtitle', 'Helpful news and care tips from our hospital team.'), $variables));
        $image = trim($this->replaceVariables((string) Arr::get($data, 'image_url', ''), $variables));
        $buttonLabel = $this->escape($this->replaceVariables((string) Arr::get($data, 'button_label', ''), $variables));
        $buttonUrl = trim($this->replaceVariables((string) Arr::get($data, 'button_url', ''), $variables));

        $imageHtml = $image !== '' ? '<img src="'.$this->escape($image).'" alt="" style="display:block;width:100%;max-height:260px;object-fit:cover;border-radius:24px 24px 0 0;">' : '';
        $buttonHtml = $buttonLabel !== '' && $buttonUrl !== '' ? $this->buttonHtml($buttonLabel, $buttonUrl, 'center', $recipient) : '';

        return <<<HTML
<tr>
<td style="padding:0 24px 18px;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:linear-gradient(135deg,#0f766e,#2563eb);border-radius:28px;overflow:hidden;color:#ffffff;">
    {$imageHtml}
    <tr><td style="padding:34px 34px 38px;text-align:center;">
      <div style="display:inline-block;border:1px solid rgba(255,255,255,.35);border-radius:999px;padding:6px 14px;font-size:12px;letter-spacing:.08em;text-transform:uppercase;">{$eyebrow}</div>
      <h1 style="margin:18px 0 12px;font-size:32px;line-height:1.15;font-weight:800;color:#ffffff;">{$title}</h1>
      <p style="margin:0 auto 22px;max-width:520px;font-size:16px;line-height:1.65;color:#e0f2fe;">{$subtitle}</p>
      {$buttonHtml}
    </td></tr>
  </table>
</td>
</tr>
HTML;
    }

    /** @param array<string, mixed> $data @param array<string, string> $variables */
    private function textBlock(array $data, array $variables): string
    {
        $alignment = in_array(Arr::get($data, 'alignment'), ['left', 'center', 'right'], true) ? (string) Arr::get($data, 'alignment') : 'left';
        $content = $this->lineBreaks($this->replaceVariables((string) Arr::get($data, 'content', 'Add your message here.'), $variables));

        return <<<HTML
<tr>
<td style="padding:14px 34px;text-align:{$alignment};">
  <div style="font-size:16px;line-height:1.75;color:#334155;">{$content}</div>
</td>
</tr>
HTML;
    }

    /** @param array<string, mixed> $data @param array<string, string> $variables */
    private function imageBlock(array $data, array $variables): string
    {
        $url = trim($this->replaceVariables((string) Arr::get($data, 'image_url', ''), $variables));
        if ($url === '') {
            return '';
        }

        $alt = $this->escape($this->replaceVariables((string) Arr::get($data, 'alt', ''), $variables));
        $caption = $this->escape($this->replaceVariables((string) Arr::get($data, 'caption', ''), $variables));
        $captionHtml = $caption !== '' ? '<div style="padding-top:10px;font-size:13px;color:#64748b;text-align:center;">'.$caption.'</div>' : '';

        return '<tr><td style="padding:18px 34px;"><img src="'.$this->escape($url).'" alt="'.$alt.'" style="display:block;width:100%;border-radius:18px;border:1px solid #e2e8f0;">'.$captionHtml.'</td></tr>';
    }

    /** @param array<string, mixed> $data @param array<string, string> $variables */
    private function buttonBlock(array $data, array $variables, ?EmailCampaignRecipient $recipient): string
    {
        $label = $this->escape($this->replaceVariables((string) Arr::get($data, 'label', 'Learn more'), $variables));
        $url = trim($this->replaceVariables((string) Arr::get($data, 'url', '#'), $variables));
        $alignment = in_array(Arr::get($data, 'alignment'), ['left', 'center', 'right'], true) ? (string) Arr::get($data, 'alignment') : 'center';

        return '<tr><td style="padding:18px 34px;text-align:'.$alignment.';">'.$this->buttonHtml($label, $url, $alignment, $recipient).'</td></tr>';
    }

    /** @param array<string, mixed> $data @param array<string, string> $variables */
    private function tipsBlock(array $data, array $variables): string
    {
        $title = $this->escape($this->replaceVariables((string) Arr::get($data, 'title', 'Helpful tips'), $variables));
        $items = Arr::get($data, 'items', []);
        if (is_string($items)) {
            $items = preg_split('/\r\n|\r|\n/', $items) ?: [];
        }

        $itemsHtml = collect($items)
            ->filter(fn ($item): bool => trim((string) $item) !== '')
            ->map(fn ($item): string => '<li style="margin:0 0 10px;padding-left:4px;">'.$this->escape($this->replaceVariables((string) $item, $variables)).'</li>')
            ->implode('');

        if ($itemsHtml === '') {
            $itemsHtml = '<li style="margin:0 0 10px;padding-left:4px;">Keep a balanced diet, regular sleep, and enough water every day.</li>';
        }

        return <<<HTML
<tr>
<td style="padding:18px 34px;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f0fdfa;border:1px solid #99f6e4;border-radius:20px;">
    <tr><td style="padding:24px 26px;">
      <h2 style="margin:0 0 14px;font-size:21px;line-height:1.35;color:#0f766e;">{$title}</h2>
      <ul style="margin:0;padding-left:22px;font-size:15px;line-height:1.65;color:#334155;">{$itemsHtml}</ul>
    </td></tr>
  </table>
</td>
</tr>
HTML;
    }

    /** @param array<string, mixed> $data @param array<string, string> $variables */
    private function ctaBlock(array $data, array $variables, ?EmailCampaignRecipient $recipient): string
    {
        $title = $this->escape($this->replaceVariables((string) Arr::get($data, 'title', 'Need medical support?'), $variables));
        $text = $this->lineBreaks($this->replaceVariables((string) Arr::get($data, 'text', 'Our appointment desk is ready to help you choose the right doctor.'), $variables));
        $label = $this->escape($this->replaceVariables((string) Arr::get($data, 'button_label', 'Book an appointment'), $variables));
        $url = trim($this->replaceVariables((string) Arr::get($data, 'button_url', config('app.url')), $variables));
        $phone = $this->escape($this->replaceVariables((string) Arr::get($data, 'phone', ''), $variables));
        $phoneHtml = $phone !== '' ? '<p style="margin:12px 0 0;font-size:14px;color:#64748b;">Call us: <strong style="color:#0f172a;">'.$phone.'</strong></p>' : '';

        return <<<HTML
<tr>
<td style="padding:18px 34px;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#ffffff;border:1px solid #dbeafe;border-radius:20px;box-shadow:0 10px 30px rgba(37,99,235,.08);">
    <tr><td style="padding:26px;text-align:center;">
      <h2 style="margin:0 0 10px;font-size:22px;color:#0f172a;">{$title}</h2>
      <p style="margin:0 auto 18px;max-width:500px;font-size:15px;line-height:1.7;color:#475569;">{$text}</p>
      {$this->buttonHtml($label, $url, 'center', $recipient)}
      {$phoneHtml}
    </td></tr>
  </table>
</td>
</tr>
HTML;
    }

    /** @param array<string, mixed> $data @param array<string, string> $variables */
    private function footerBlock(array $data, array $variables): string
    {
        $address = $this->lineBreaks($this->replaceVariables((string) Arr::get($data, 'address', '{{ hospital_name }}'), $variables));

        return '<tr><td style="padding:22px 34px 0;text-align:center;font-size:13px;line-height:1.65;color:#64748b;">'.$address.'</td></tr>';
    }

    private function buttonHtml(string $label, string $url, string $alignment = 'center', ?EmailCampaignRecipient $recipient = null): string
    {
        $trackedUrl = $this->trackedUrl($url, $recipient);

        return '<a href="'.$this->escape($trackedUrl).'" style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;border-radius:999px;padding:13px 24px;font-size:15px;font-weight:700;box-shadow:0 8px 20px rgba(37,99,235,.22);">'.$label.'</a>';
    }

    /** @param array<string, string> $variables */
    private function wrapHtml(string $subject, string $preheader, string $body, array $variables, ?EmailCampaignRecipient $recipient = null): string
    {
        $appName = $this->escape($variables['hospital_name'] ?? config('app.name', 'Hospital'));
        $safeSubject = $this->escape($subject);
        $safePreheader = $this->escape($preheader);
        $unsubscribeUrl = $this->escape($variables['unsubscribe_url'] ?? '#');
        $browserUrl = $this->escape($variables['campaign_url'] ?? config('app.url'));
        $openPixel = $recipient && $recipient->exists
            ? '<img src="'.$this->escape(route('emailmarketing.track.open', ['recipient' => $recipient->id, 'token' => $recipient->tracking_token])).'" width="1" height="1" alt="" style="display:none;max-height:1px;max-width:1px;opacity:0;overflow:hidden;">'
            : '';

        return <<<HTML
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{$safeSubject}</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,'Helvetica Neue',Helvetica,sans-serif;color:#334155;">
  <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">{$safePreheader}</div>
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f1f5f9;padding:28px 0;">
    <tr>
      <td align="center" style="padding:0 12px;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;background:#ffffff;border-radius:30px;overflow:hidden;box-shadow:0 18px 60px rgba(15,23,42,.12);">
          <tr>
            <td style="padding:24px 34px 10px;text-align:center;">
              <div style="font-size:13px;letter-spacing:.08em;text-transform:uppercase;color:#0f766e;font-weight:800;">{$appName}</div>
            </td>
          </tr>
          {$body}
          <tr>
            <td style="padding:28px 34px 34px;text-align:center;font-size:12px;line-height:1.65;color:#94a3b8;">
              You received this email because you subscribed to {$appName} updates.<br>
              <a href="{$unsubscribeUrl}" style="color:#2563eb;text-decoration:underline;">Unsubscribe</a>
              <span style="color:#cbd5e1;"> • </span>
              <a href="{$browserUrl}" style="color:#2563eb;text-decoration:underline;">Visit website</a>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  {$openPixel}
</body>
</html>
HTML;
    }

    /** @return array<string, string> */
    private function variables(EmailCampaign $campaign, ?EmailCampaignRecipient $recipient = null): array
    {
        $newsletter = $recipient?->newsletter;
        $email = $recipient?->email ?: 'subscriber@example.com';
        $name = $recipient?->name ?: $newsletter?->name ?: 'Subscriber';
        $unsubscribeUrl = '#';

        if ($newsletter && $newsletter->unsubscribe_token) {
            $unsubscribeUrl = route('newsletter.unsubscribe', ['newsletter' => $newsletter->id, 'token' => $newsletter->unsubscribe_token]);
        }

        return [
            'name' => $name,
            'email' => $email,
            'first_name' => Str::of($name)->explode(' ')->first() ?: $name,
            'hospital_name' => config('app.name', 'Hospital'),
            'app_url' => config('app.url'),
            'campaign_name' => $campaign->name ?: 'Hospital update',
            'unsubscribe_url' => $unsubscribeUrl,
            'campaign_url' => config('app.url'),
            'today' => now()->toFormattedDateString(),
        ];
    }

    /** @param array<string, string> $variables */
    private function replaceVariables(string $value, array $variables): string
    {
        foreach ($variables as $key => $replacement) {
            $value = str_replace(['{{ '.$key.' }}', '{{'.$key.'}}'], $replacement, $value);
        }

        return $value;
    }

    private function trackedUrl(string $url, ?EmailCampaignRecipient $recipient = null): string
    {
        if ($url === '' || $url === '#') {
            return $url ?: '#';
        }

        if (! $recipient || ! $recipient->exists || ! $recipient->tracking_token) {
            return $url;
        }

        return URL::route('emailmarketing.track.click', [
            'recipient' => $recipient->id,
            'token' => $recipient->tracking_token,
            'url' => $url,
        ]);
    }

    private function escape(string $value): string
    {
        return e($value, false);
    }

    private function lineBreaks(string $value): string
    {
        return nl2br($this->escape($value));
    }

    /** @return array<int, array<string, mixed>> */
    private function defaultBlocks(): array
    {
        return [
            [
                'type' => 'hero',
                'data' => [
                    'eyebrow' => 'Hospital update',
                    'title' => 'Your health matters, {{ first_name }}',
                    'subtitle' => 'Fresh updates, practical tips, and helpful reminders from our hospital team.',
                    'button_label' => 'Visit website',
                    'button_url' => '{{ app_url }}',
                ],
            ],
            [
                'type' => 'text',
                'data' => [
                    'content' => 'Thank you for staying connected with us. We are committed to helping you make informed healthcare decisions.',
                ],
            ],
        ];
    }
}
