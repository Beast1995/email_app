<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Welcome Email',
                'subject' => 'Welcome to {{company_name}}, {{name}}!',
                'content' => '
                    <h2>Welcome to {{company_name}}!</h2>
                    <p>Dear {{name}},</p>
                    <p>Thank you for joining {{company_name}}. We are excited to have you as part of our community!</p>
                    <p>Here are some things you can do to get started:</p>
                    <ul>
                        <li>Explore our products and services</li>
                        <li>Check out our latest updates</li>
                        <li>Connect with our support team</li>
                    </ul>
                    <p>If you have any questions, feel free to reach out to us at {{support_email}}.</p>
                    <p>Best regards,<br>The {{company_name}} Team</p>
                ',
                'variables' => ['name', 'company_name', 'support_email'],
                'is_active' => true
            ],
            [
                'name' => 'Newsletter Template',
                'subject' => '{{company_name}} Newsletter - {{month_year}}',
                'content' => '
                    <h2>{{company_name}} Newsletter</h2>
                    <p>Hello {{name}},</p>
                    <p>Here\'s what\'s new this month:</p>
                    <h3>Latest Updates</h3>
                    <p>{{news_content}}</p>
                    <h3>Upcoming Events</h3>
                    <p>{{events_content}}</p>
                    <p>Stay tuned for more exciting updates!</p>
                    <p>Best regards,<br>The {{company_name}} Team</p>
                ',
                'variables' => ['name', 'company_name', 'month_year', 'news_content', 'events_content'],
                'is_active' => true
            ],
            [
                'name' => 'Promotional Email',
                'subject' => 'Special Offer for {{name}} - {{discount_percentage}}% Off!',
                'content' => '
                    <h2>Special Offer Just for You!</h2>
                    <p>Dear {{name}},</p>
                    <p>We have a special offer that we think you\'ll love!</p>
                    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center; margin: 20px 0;">
                        <h3 style="color: #dc3545; margin: 0;">{{discount_percentage}}% OFF</h3>
                        <p style="margin: 10px 0;">Use code: <strong>{{promo_code}}</strong></p>
                        <p style="margin: 0;">Valid until {{expiry_date}}</p>
                    </div>
                    <p>Don\'t miss out on this amazing deal!</p>
                    <p>Best regards,<br>The {{company_name}} Team</p>
                ',
                'variables' => ['name', 'company_name', 'discount_percentage', 'promo_code', 'expiry_date'],
                'is_active' => true
            ],
            [
                'name' => 'Product Announcement',
                'subject' => 'New Product Launch: {{product_name}}',
                'content' => '
                    <h2>Introducing {{product_name}}!</h2>
                    <p>Dear {{name}},</p>
                    <p>We are excited to announce the launch of our newest product: <strong>{{product_name}}</strong></p>
                    <h3>Key Features:</h3>
                    <ul>
                        <li>{{feature_1}}</li>
                        <li>{{feature_2}}</li>
                        <li>{{feature_3}}</li>
                    </ul>
                    <p>Get yours today and be among the first to experience this amazing new product!</p>
                    <p>Best regards,<br>The {{company_name}} Team</p>
                ',
                'variables' => ['name', 'company_name', 'product_name', 'feature_1', 'feature_2', 'feature_3'],
                'is_active' => true
            ]
        ];

        foreach ($templates as $template) {
            EmailTemplate::create($template);
        }
    }
} 