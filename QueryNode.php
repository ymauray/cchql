<?php 

class QueryNode {

    var $relations;
    var $where;
    var $return_statement;

    public function QueryNode($relations, $where, $return_statement) {
        $this->relations = $relations;
        $this->where = $where;
        $this->return_statement = $return_statement;
    }
}
