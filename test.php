<?php
require "QueryNode.php";
require "RelationsNode.php";
require "TableRefNode.php";
require "TableAliasNode.php";
require "RelationNode.php";

require "ParseErrorException.php";

$query = "MATCH (a:artists)-[id:artist_id]-(t:tracks)-[id:track_id]-(st:showtracks)-[show_id:id]-(s:shows) WHERE t.title CONTAINS 'flower' AND NOT (s.show_name CONTAINS 'bugcast' AND s.url CONTAINS 'bugcast.org') RETURN t, a;";
$position = 0;

$terminals = [
    "/^(MATCH)/" => "MATCH",
    "/^(\()/" => "OPEN_PAREN",
    "/^(\))/" => "CLOSE_PAREN",
    "/^([a-zA-Z_][a-zA-Z0-9_]*)/" => "ID",
    "/^(:)/" => "COLON",
    "/^(-)/" => "HYPHEN",
    "/^(\[)/" => "OPEN_BRACKET",
    "/^(\])/" => "CLOSE_BRACKET"
];

$current_tokens = NULL;

next_token();
$node = query();
print json_encode($node);

function query() {
    if (!accept_token("MATCH")) throw new ParseErrorException("'MATCH' expected");
    $relations = relations();
    $where = where();
    $return_statement = return_statement();
    return new QueryNode($relations, $where, $return_statement);
}

function relations() {
    $table_ref = table_ref();
    $relations = [];
    while (accept_token("HYPHEN", true)) {
        $relation = relation();
        $relations[] = $relation;
    }
    return new RelationsNode($table_ref, $relations);
}

function table_ref() {
    if (!accept_token("OPEN_PAREN")) throw new ParseErrorException("'(' expected");
    $table_alias = table_alias();
    if (!accept_token("CLOSE_PAREN")) throw new ParseErrorException("')' expected");
    return new TableRefNode($table_alias);
}

function table_alias() {
    $id = accept_token("ID");
    if ($id === FALSE) throw new ParseErrorException("identifier expected");
    if (accept_token("COLON")) {
        $alias = $id;
        $table = accept_token("ID");
        if ($table === FALSE) throw new ParseErrorException("identifier expected");
    }
    else {
        $alias = NULL;
        $table = $id;
    }
    return new TableAliasNode($table, $alias);
}

function relation() {
    if (!accept_token("HYPHEN")) throw new ParseErrorException("'-' expected");
    if (accept_token("OPEN_BRACKET")) {
        $left_key = accept_token("ID");
        if (!accept_token("COLON")) throw new ParseErrorException("':' expected");
        $right_key = accept_token("ID");
        if (!accept_token("CLOSE_BRACKET")) throw new ParseErrorException("']' expected");
        if (!accept_token("HYPHEN")) throw new ParseErrorException("'-' expected");
    }
    $table_ref = table_ref();
    return new RelationNode($left_key, $right_key, $table_ref);
}

function where() {
    if (!accept_token("WHERE")) throw new ParseErrorException("'WHERE' expected");
    $conditions = conditions();
    return new WhereNode($conditions);
}

function return_statement() {

}

function next_token() {
    global $position, $query, $current_tokens, $terminals;
    
    $string = substr($query, $position);
    $current_tokens = [];
    foreach($terminals as $regex => $token) {
        if (preg_match($regex, $string, $matches)) {
            $current_tokens[] = [
                "token" => $token,
                "value" => $matches[1]
            ];
        }
    }
}

function accept_token($token, $peek = FALSE) {
    global $current_tokens, $position, $query;
    $string = substr($query, $position);
    $ws = "/^([ \t\n\r\f]+)/";
    foreach($current_tokens as $current_token) {
        if ($current_token["token"] == $token) {
            $value = $current_token["value"];
            if (!$peek) {
                $position += strlen($value);
                $string = substr($query, $position);
                if (preg_match($ws, substr($query, $position), $matches)) {
                    $position += strlen($matches[1]);
                    $string = substr($query, $position);
                }
                next_token();
            }
            return $value;
        }
    }
    return false;
}

