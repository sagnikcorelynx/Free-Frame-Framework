<?php

namespace Core;

abstract class Migration
{
    abstract public function up();
    abstract public function down();
}
