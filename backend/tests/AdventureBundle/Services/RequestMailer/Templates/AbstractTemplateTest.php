<?php

namespace Tests\AdventureBundle\Services\RequestMailer\Templates;

use Divante\Bundle\AdventureBundle\Entity\LeaveRequest;
use Divante\Bundle\AdventureBundle\Entity\LeaveRequestDay;
use Divante\Bundle\AdventureBundle\Services\RequestMailer\Templates\AbstractTemplate;
use Symfony\Component\Translation\Translator;
use function PHPSTORM_META\elementType;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AbstractTemplateTest extends WebTestCase
{
    /** @var Translator */
    protected $translator;

    public function testCorrectlyPassedAdminEmail() : void
    {
        $this->translator = new Translator('pl');
        $randomEmail = 'RandomAdmEmail'.rand(0, 1000).'@example.com';
        $randomTechEmail = 'RandomTechEmail'.rand(0, 1000).'@example.com';
        $randomAccountantEmail = 'RandomAccountantEmail'.rand(0, 1000).'@example.com';
        $params = [
            [$randomEmail],
            $randomTechEmail,
            $randomAccountantEmail,
            '', new LeaveRequest(),
            $this->translator,
            ''
        ];
        $template = $this->getMockForAbstractClass(AbstractTemplate::class, $params);
        $templateReflection = new \ReflectionObject($template);
        $method = $templateReflection->getMethod('getAdminEmails');
        $method->setAccessible(true);
        $result = $method->invokeArgs($template, []);
        $this->assertIsString($result[0]);
        $this->assertEquals($randomEmail, $result[0]);
    }

    public function testTemplateData() : void
    {
        $this->translator = new Translator('pl');
        $frontedAppUrl = "RandomFrontendAppUrl".rand(0, 100000);
        $request = new LeaveRequest();
        $params = [ [''], '', '', $frontedAppUrl, $request, $this->translator, ''];
        $template = $this->getMockForAbstractClass(AbstractTemplate::class, $params);

        $templateData = $template->getData();
        $this->assertArrayHasKey('request', $templateData);
        $this->assertArrayHasKey('dateNow', $templateData);
        $this->assertArrayHasKey('frontend_app_url', $templateData);

        $this->assertEquals($request, $templateData['request']);
        $this->assertEquals($frontedAppUrl, $templateData['frontend_app_url']);
        $dateTime = \DateTime::createFromFormat(AbstractTemplate::DATE_FORMAT, $templateData['dateNow']);
        $timestamp = $dateTime->getTimestamp();
        $diff = time() - $timestamp;
        $this->assertLessThan(60, $diff);
    }
    
    public function testSpecialTypeSign() : void
    {
        $this->translator = new Translator('pl');
        $types = [
            LeaveRequestDay::DAY_TYPE_FREE_PAID,
            LeaveRequestDay::DAY_TYPE_FREE_UNPAID,
            LeaveRequestDay::DAY_TYPE_LEAVE_PAID,
            LeaveRequestDay::DAY_TYPE_LEAVE_UNPAID,
            LeaveRequestDay::DAY_TYPE_LEAVE_REQUEST,
            LeaveRequestDay::DAY_TYPE_LEAVE_OCCASIONAL,
            LeaveRequestDay::DAY_TYPE_LEAVE_CARE,
            LeaveRequestDay::DAY_TYPE_SICK_LEAVE_PAID,
            LeaveRequestDay::DAY_TYPE_SICK_LEAVE_UNPAID
        ];
        foreach ($types as $type) {
            $requestDay = new LeaveRequestDay();
            $requestDay->setType($type);
            $request = new LeaveRequest();
            $request->getRequestDays()->add($requestDay);
            $params = [ [''], '', '', '', $request, $this->translator, '' ];
            $template = $this->getMockForAbstractClass(AbstractTemplate::class, $params);

            $specialTypeSign = $template->getSpecialTypeSign();
            if ($type === LeaveRequestDay::DAY_TYPE_SICK_LEAVE_PAID) {
                $this->assertEquals("[\u{26C7}]", $specialTypeSign);
            } elseif ($type === LeaveRequestDay::DAY_TYPE_SICK_LEAVE_UNPAID) {
                $this->assertEquals("[\u{26C7}]", $specialTypeSign);
            } elseif ($type === LeaveRequestDay::DAY_TYPE_LEAVE_CARE) {
                $this->assertEquals('[♚]', $specialTypeSign);
            } elseif ($type === LeaveRequestDay::DAY_TYPE_LEAVE_OCCASIONAL) {
                $this->assertEquals('[☘]', $specialTypeSign);
            } else {
                $this->assertEquals('', $specialTypeSign);
            }
        }
    }
}
