<?php

namespace Tests\Unit\Chatbot;

use App\Services\Chatbot\QuickReplyGenerator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuickReplyGeneratorTest extends TestCase
{
    #[Test]
    public function it_suggests_amounts_for_simulation_without_amount(): void
    {
        $generator = new QuickReplyGenerator();

        $replies = $generator->generate('Saya mau simulasi cicilan', 'Baik, silakan jumlah pokoknya.');

        $this->assertContains('💵 Simulasi Rp 50 Juta', $replies);
        $this->assertContains('✏️ Masukkan Jumlah Manual', $replies);
    }

    #[Test]
    public function it_suggests_methods_when_amount_is_present(): void
    {
        $generator = new QuickReplyGenerator();

        $replies = $generator->generate('Simulasi Rp 100 Juta', 'Siap, pilih metode.');

        $this->assertContains('📊 Pilih Metode Flat', $replies);
        $this->assertContains('📈 Pilih Metode Anuitas', $replies);
    }
}
