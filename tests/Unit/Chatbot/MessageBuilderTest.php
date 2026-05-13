<?php

namespace Tests\Unit\Chatbot;

use App\Services\Chatbot\MessageBuilder;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MessageBuilderTest extends TestCase
{
    #[Test]
    public function it_builds_messages_with_system_prompt_and_history(): void
    {
        $builder = new MessageBuilder();

        $messages = $builder->build('SYS', [
            ['role' => 'user', 'content' => 'Hi'],
            ['role' => 'model', 'content' => 'Hello'],
        ], 'New question');

        $this->assertSame('system', $messages[0]['role']);
        $this->assertSame('SYS', $messages[0]['content']);
        $this->assertSame('user', $messages[1]['role']);
        $this->assertSame('assistant', $messages[2]['role']);
        $this->assertSame('New question', $messages[3]['content']);
    }
}
