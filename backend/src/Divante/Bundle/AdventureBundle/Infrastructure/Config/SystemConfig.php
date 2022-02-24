<?php

namespace Divante\Bundle\AdventureBundle\Infrastructure\Config;

use Divante\Bundle\AdventureBundle\Entity\ConfigEntry;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use ReflectionException;

class SystemConfig
{
    private EntityManagerInterface $em;

    public const KEY_SLACK_CLIENT_ID = 'slack.client_id';
    public const KEY_SLACK_CLIENT_SECRET = 'slack.client_secret';
    public const KEY_SLACK_REDIRECT_URI = 'slack.redirect_uri';
    public const KEY_SLACK_ADMIN_ID = 'slack.admin_id';
    public const KEY_SLACK_ADMIN_ACCESS_TOKEN = 'slack.admin_access_token';
    public const KEY_SLACK_ADMIN_STATUS = 'slack.admin_status';

    public const KEY_GITLAB_INSTANCE_URL = 'gitlab.instance_url';
    public const KEY_GITLAB_TOKEN = 'gitlab.token';

    public const KEY_SNIPE_IT_INSTANCE_URL = 'snipe_it.instance_url';
    public const KEY_SNIPE_IT_TOKEN = 'snipe_it.token';

    public const KEY_AVAZA_TOKEN = 'avaza.token';
    public const KEY_AVAZA_SICK_LEAVE_PROJECT_ID = 'avaza.sick_leave_project_id';
    public const KEY_AVAZA_SICK_LEAVE_CATEGORY_ID = 'avaza.sick_leave_category_id';
    public const KEY_AVAZA_FREE_DAY_PROJECT_ID = 'avaza.free_day_project_id';
    public const KEY_AVAZA_FREE_DAY_CATEGORY_ID = 'avaza.free_day_category_id';

    public const KEY_TRIBE_CONNECTION_1_ID = 'tribe.connected1_id';
    public const KEY_TRIBE_CONNECTION_2_ID = 'tribe.connected2_id';

    public const CONTENT_AGREEMENT_GENERAL_EN = 'content.agreement_general_en';
    public const CONTENT_AGREEMENT_OHS_EN = 'content.agreement_ohs_en';
    public const CONTENT_AGREEMENT_GDPR_EN = 'content.agreement_gdpr_en';
    public const CONTENT_AGREEMENT_ISO_EN = 'content.agreement_iso_en';
    public const CONTENT_AGREEMENT_MARKETING_EN = 'content.agreement_marketing_en';
    public const CONTENT_AGREEMENT_MARKETING_MAIN_EN = 'content.agreement_marketing_main_en';
    public const CONTENT_AGREEMENT_GENERAL_PL = 'content.agreement_general_pl';
    public const CONTENT_AGREEMENT_OHS_PL = 'content.agreement_ohs_pl';
    public const CONTENT_AGREEMENT_GDPR_PL = 'content.agreement_gdpr_pl';
    public const CONTENT_AGREEMENT_ISO_PL = 'content.agreement_iso_pl';
    public const CONTENT_AGREEMENT_MARKETING_PL = 'content.agreement_marketing_pl';
    public const CONTENT_AGREEMENT_MARKETING_MAIN_PL = 'content.agreement_marketing_main_pl';
    public const CONTENT_ISO_LINK_PL = 'content.iso_link_pl';
    public const CONTENT_ISO_LINK_EN = 'content.iso_link_en';

    public const CONTENT_EMAIL_DOMAIN = 'content.email_domain';
    public const CONTENT_WORK_MODE_LINK = 'content.work_mode_link';

    public const KEY_PAYROLL_EMAIL_FOR_B2B_PL = 'payroll.email_for_b2b_pl';
    public const KEY_PAYROLL_EMAIL_FOR_B2B_DE = 'payroll.email_for_b2b_de';

    public const KEY_PAYROLL_EMAIL_FOR_NOT_B2B_PL = 'payroll.email_for_not_b2b_pl';
    public const KEY_PAYROLL_EMAIL_FOR_NOT_B2B_DE = 'payroll.email_for_not_b2b_de';

    public const KEY_EVIDENCE_EMAIL_FOR_B2B_PL = 'evidence.email_for_b2b_pl';
    public const KEY_EVIDENCE_EMAIL_FOR_B2B_DE = 'evidence.email_for_b2b_de';

    public const KEY_EVIDENCE_EMAIL_FOR_NOT_B2B_PL = 'evidence.email_for_not_b2b_pl';
    public const KEY_EVIDENCE_EMAIL_FOR_NOT_B2B_DE = 'evidence.email_for_not_b2b_de';

    public const KEY_PAYROLL_EMAIL_FOR_COE_PL = 'payroll.email_for_coe_pl';
    public const KEY_PAYROLL_EMAIL_FOR_COE_DE = 'payroll.email_for_coe_de';

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return string[]
     * @param string $key
     * @param int $nr
     * @throws ReflectionException
     */
    public function getKeys(string $key, int $nr): array
    {
        $ref = new ReflectionClass(self::class);
        /** @var array<string,mixed> $constants */
        $constants = $ref->getConstants();
        $result = [];
        foreach ($constants as $constName => $constValue) {
            if (substr($constName, 0, $nr) === $key) {
                $result[] = $constValue;
            }
        }
        return $result;
    }

    /** @return ConfigEntry[] */
    public function getContentValues(): array
    {
        $repo = $this->em->getRepository(ConfigEntry::class);
        /** @var ConfigEntry[] $entries */
        $entries = $repo->findBy([
            'replacedAt' => null,
            'group' => 'content',
        ]);
        return $entries;
    }

    public function getValue(string $key): ?ConfigEntry
    {
        $repo = $this->em->getRepository(ConfigEntry::class);
        /** @var ConfigEntry|null $entry */
        $entry = $repo->findOneBy([
            'key' => $key,
            'replacedAt' => null,
        ]);
        return $entry;
    }

    public function getValueOrDefault(string $key, string $default): string
    {
        $entry = $this->getValue($key);
        return is_null($entry) ? $default : $entry->getValue();
    }

    /**
     * @param string $key
     * @return ConfigEntry[]
     */
    public function getValueHistory(string $key): array
    {
        $repo = $this->em->getRepository(ConfigEntry::class);
        /** @var ConfigEntry[] $entries */
        $entries = $repo->findBy([
            'key' => $key,
        ]);
        return $entries;
    }
}
