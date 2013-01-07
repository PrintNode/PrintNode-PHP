<?php

interface PrintNode_EntityInterface
{
    /**
     * Return an array of properties that
     * are foreign keys and relate to other PrintNode_Entity
     * objects
     * @param void
     * @return string[]
     */
    public function foreignKeyEntityMap();
}