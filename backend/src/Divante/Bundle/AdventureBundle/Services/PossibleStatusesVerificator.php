<?php

namespace Divante\Bundle\AdventureBundle\Services;

class PossibleStatusesVerificator
{
    /**
     * Checks if given array of possible statuses are correct. If they are, does nothing, otherwise throws Exception.
     * @param array<int,array<string,string|bool>> $possibleStatuses
     * @throws \Exception
     */
    public function verify(array $possibleStatuses) : void
    {
        if (count($possibleStatuses) < 2) {
            throw new \Exception("Only ".count($possibleStatuses)." statuses passed, required at least 2");
        }
        $default = 0;
        $done = 0;
        /** @var array<string,string|bool> $status */
        foreach ($possibleStatuses as $key => $status) {
            if (!array_key_exists('label_pl', $status) || empty($status['label_pl'])) {
                throw new \Exception("Status at index $key does not have 'label_pl' field.");
            }
            if (!array_key_exists('label_en', $status) || empty($status['label_en'])) {
                throw new \Exception("Status at index $key does not have 'label_en' field.");
            }
            if (!array_key_exists('color', $status) || empty($status['color'])) {
                throw new \Exception("Status at index $key does not have 'color' field.");
            }
            if (!array_key_exists('icon', $status) || empty($status['icon'])) {
                throw new \Exception("Status at index $key does not have 'icon' field.");
            }
            if ($status['default'] ?? false) {
                $default++;
            }
            if ($status['done'] ?? false) {
                $done++;
            }
        }
        if ($default === 0) {
            throw new \Exception("There is no status with 'default' set to true.");
        }
        if ($default > 1) {
            throw new \Exception("There are too many statuses with 'default' set to true.");
        }
        if ($done === 0) {
            throw new \Exception("There is no status with 'done' set to true.");
        }
    }
}
