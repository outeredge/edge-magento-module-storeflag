<?php

$this->startSetup()->run("
    ALTER TABLE `{$this->getTable('core/store')}`
        ADD COLUMN `flag` text NULL DEFAULT NULL;
")->endSetup();
