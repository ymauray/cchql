<?php

class RelationsNode {
    var $first_table;
    var $relations;

    public function RelationsNode($first_table, $relations) {
        $this->first_table = $first_table;
        $this->relations = $relations;
    }
}