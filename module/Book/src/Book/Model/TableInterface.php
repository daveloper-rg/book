<?php
namespace Book\Model;

interface TableInterface{
    public function getEndpoint();
    public function buildStructure($data);
}