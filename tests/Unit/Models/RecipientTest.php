<?php

namespace GloCurrency\FidelityBank\Tests\Unit\Jobs;

use Illuminate\Database\Eloquent\SoftDeletes;
use GloCurrency\FidelityBank\Tests\TestCase;
use GloCurrency\FidelityBank\Models\Recipient;
use BrokeYourBike\BaseModels\BaseUuid;

class RecipientTest extends TestCase
{
    /** @test */
    public function it_extends_base_model(): void
    {
        $parent = get_parent_class(Recipient::class);

        $this->assertSame(BaseUuid::class, $parent);
    }

    /** @test */
    public function it_uses_soft_deletes(): void
    {
        $usedTraits = class_uses(Recipient::class);

        $this->assertArrayHasKey(SoftDeletes::class, $usedTraits);
    }

    /** @test */
    public function it_can_return_country_code_alpha2()
    {
        $recipient = new Recipient();
        $recipient->country_code = 'USA';

        $this->assertSame('US', $recipient->country_code_alpha2);
    }
}
