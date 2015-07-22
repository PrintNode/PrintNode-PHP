<?php

namespace PrintNode;

interface EntityInterface
{
    /**
     * Return an array of properties that
     * are foreign keys and relate to other Entity
     * objects
     * @param void
     * @return string[]
     */
    public function foreignKeyEntityMap();
    
}
