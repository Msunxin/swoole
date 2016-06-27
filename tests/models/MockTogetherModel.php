<?php

namespace tests\models;


class MockTogetherModel
{
    public function getUserTogether($userId, $specId)
    {
        $together = json_decode(file_get_contents(TEST_RESOURCES_DIR . '/user-together-data.json'), true);
        return $together;
    }
}