<?php

namespace Tests\AdventureBundle\Services;

use Divante\Bundle\AdventureBundle\Services\PossibleStatusesVerificator;
use Tests\FoundationTestCase;

class PossibleStatusesVerificatorTest extends FoundationTestCase
{
    public function testNoStatuses() : void
    {
        $statuses = [];
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches("/.*at least 2.*/i");
        $this->getVerificator()->verify($statuses);
    }

    public function testOneStatus() : void
    {
        $statuses = [ $this->generateStatus() ];
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches("/.*at least 2.*/i");
        $this->getVerificator()->verify($statuses);
    }

    public function testNoStatusHasDefault() : void
    {
        $statuses = [];
        for ($i = 0; $i < 5; $i++) {
            $statuses[] = $this->generateStatus();
        }
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches("/.*no.*default.*/i");
        $this->getVerificator()->verify($statuses);
    }

    public function testTooManyStatusWithDefault() : void
    {
        $statuses = [];
        for ($i = 0; $i < 5; $i++) {
            $status = $this->generateStatus();
            $status['default'] = true;
            $statuses[] = $status;
        }
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches("/.*too.*many.*default.*/i");
        $this->getVerificator()->verify($statuses);
    }

    public function testNoStatusDone() : void
    {
        $statuses = [];
        for ($i = 0; $i < 5; $i++) {
            $statuses[] = $this->generateStatus();
        }
        $statuses[0]['default'] = true;
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches("/.*no.*done.*/i");
        $this->getVerificator()->verify($statuses);
    }

    public function testLackingLabelPl() : void
    {
        $statuses = [];
        for ($i = 0; $i < 5; $i++) {
            $statuses[] = $this->generateStatus();
        }
        unset($statuses[0]['label_pl']);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches("/.*not.*label_pl.*/i");
        $this->getVerificator()->verify($statuses);
    }

    public function testLackingLabelEn() : void
    {
        $statuses = [];
        for ($i = 0; $i < 5; $i++) {
            $statuses[] = $this->generateStatus();
        }
        unset($statuses[0]['label_en']);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches("/.*not.*label_en.*/i");
        $this->getVerificator()->verify($statuses);
    }

    public function testLackingIcon() : void
    {
        $statuses = [];
        for ($i = 0; $i < 5; $i++) {
            $statuses[] = $this->generateStatus();
        }
        unset($statuses[0]['icon']);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches("/.*not.*icon.*/i");
        $this->getVerificator()->verify($statuses);
    }

    public function testLackingColor() : void
    {
        $statuses = [];
        for ($i = 0; $i < 5; $i++) {
            $statuses[] = $this->generateStatus();
        }
        unset($statuses[0]['color']);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches("/.*not.*color.*/i");
        $this->getVerificator()->verify($statuses);
    }

    private function getVerificator() : PossibleStatusesVerificator
    {
        return new PossibleStatusesVerificator();
    }

    private function generateStatus() : array
    {
        return [
            'label_pl' => "RandomLabelPl".rand(0, 10000),
            'label_en' => "RandomLabelEn".rand(0, 10000),
            'icon' => "RandomIcon".rand(0, 10000),
            'color' => "RandomColor".rand(0, 10000),
        ];
    }
}
