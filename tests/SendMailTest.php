<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use LionMailer\Mailer;

class SendMailTest extends TestCase {

	public function setUp(): void {
        (\Dotenv\Dotenv::createImmutable(__DIR__ . "/../"))->load();

        Mailer::init([
            'debug' => (int) $_ENV['MAIL_DEBUG'],
            'host' => $_ENV['MAIL_HOST'],
            'username' => $_ENV['MAIL_USERNAME'],
            'password' => $_ENV['MAIL_PASSWORD'],
            'encryption' => $_ENV['MAIL_ENCRYPTION'],
            'port' => (int) $_ENV['MAIL_PORT'],
        ]);
    }

    public function testSendMail(): void {
        $responseEmail = Mailer::from('example-dev@outlook.com')
            ->address('example-dev@outlook.com')
            ->replyTo('example-dev@outlook.com')
            ->subject('subject test')
            ->body('body test')
            ->altBody('altbody test')
            ->send();

        $this->assertEquals('success', $responseEmail->status);
    }

}