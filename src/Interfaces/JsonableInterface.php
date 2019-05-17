<?php

namespace XnLibrary\Interfaces;

interface JsonableInterface
{
    public function toJsonArray(array $options = []): array;
}
