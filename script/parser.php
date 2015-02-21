<?php

require 'vendor/autoload.php';

// http://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || strpos($haystack, $needle, strlen($haystack) - strlen($needle)) !== FALSE;
}

function el_bulli_string_decode($str) {
    $result = [];

    $items = explode('&', $str);

    foreach($items as $item) {
        $parts = explode('=', trim($item));
        $key = trim($parts[0]);
        $value = trim($parts[1]);

        if(empty($key)) {
            $inter = var_export($result);
            throw new Exception("Invalid key for recipe string: [[[$item]]]\nIntermediate value: [[[$inter)]]]");
        }

        $result[$key] = $value;
    }

    return $result;
}

class ElBulliIngredient {
    public $recipe_id;
    public $text = '';
    public $amount;
    public $unit;
    public $header;

    public function __construct($text, $header = null, $recipe_id = null) {
        $this->text = $text;
        $this->header = $header;
        $this->recipe_id = $recipe_id;
        $this->parse_amount();
        $this->parse_unit();
    }

    public function append($str) {
        $this->text .= ' ' . $str;
    }

    private function parse_amount() {
        $matches = [];
        if(preg_match('/^[\d|\.|,]+/', $this->text, $matches)) {
            $this->amount = $matches[0];
        }
    }

    private function parse_unit() {
        $matches = [];
        if(preg_match('/^[\d|\.|,]+\ ?\w\ /', $this->text, $matches)) {
            $this->unit = substr(trim($matches[0]), -1);
        }
    }

}

class ElBulliRecipe {
    // Reserved
    private $str;
    private $data = [];

    // Attributes
    public $id;
    public $title;

    public $temperature; // Hot or cold dish
    public $people = 0;  // Amount of people
    public $year; // Year created
    public $season = 'all year';
    public $description;
    public $presentation;

    public $category;
    public $cookbook;

    public $ingredients = [];

    public function __construct($str, array $defaults = []) {
        $utf8_str = iconv('utf-16le', 'utf-8', $str);
        $this->data = el_bulli_string_decode($utf8_str);
        $this->str = $utf8_str;

        if(!empty($defaults))
            $this->assign_defaults($defaults);

        $this->assign_id();
        $this->assign_attributes();
        $this->assign_ingredients();
        $this->assign_description();
        $this->assign_presentation();
    }

    /**
     * Which assigned attributes to copy over.
     * @var array
     */
    private static $assignments = [
        'num' => 'id',
        'titol' => 'title',
        'any' => 'year',
        'pers' => 'people',
        'familia' => 'category',
        'temporada' => 'season',
        'temperatura' => 'temperature',
    ];

    /**
     * Assign common, single-valued attributes.
     */
    private function assign_attributes() {
        foreach(static::$assignments as $k => $v) {
            if(array_key_exists($k, $this->data))
                $this->$v = $this->data[$k];
        }
    }

    private function assign_defaults(array $params) {
        foreach($params as $k => $v) {
            if(property_exists($this, $k))
                $this->$k = $v;
        }
    }

    /**
     * Populate the ingredients array.
     */
    private function assign_ingredients() {
        $current_header = null;
        foreach($this->data as $k => $v) {
            if(startsWith($k, 'titolelaboracio')) {
                $current_header = $this->data[$k];
            } elseif(startsWith($k, 'ingredientselaboracio')) {
                $this->addIngredient($this->data[$k], $current_header);
            }
        }
    }

    /**
     * Push a new ingredient to the end of the list.
     */
    public function addIngredient($str, $header) {
        $parts = explode('#', $str);
        foreach($parts as $ingredient) {
            $ingredient = trim($ingredient);
            if(empty($ingredient)) continue;

            // Append if the ingredient contains a closing paren ')' and no opening paren '('.
            if(preg_match('/^([^\(]+)(\)){1}$/', $ingredient) == 1 ||
                    substr($ingredient, 0, 1) == '(') {
                end($this->ingredients);
                $last = key($this->ingredients);
                $this->ingredients[$last]->append($ingredient);
            } else {
                $this->ingredients[] = new ElBulliIngredient($ingredient, $header);
            }
        }
    }

    /**
     * Build the recipe description.
     */
    private function assign_description() {
        // descripcio
        foreach($this->data as $k => $v) {
            if(startsWith($k, 'titolelaboracio')) {
                $head = $this->data[$k];
                $this->description .= "### $head\n\n";
            } elseif(startsWith($k, 'descripcioelaboracio')) {
                $desc = $this->data[$k];
                $desc = str_replace('#', "\n", $desc);
                $this->description .= $desc;
                $this->description .= "\n\n";
            }
        }
        if(!empty($this->description))
            $this->description = trim($this->description);
    }

    private function assign_presentation() {
        // acabatipresentacio
        if(array_key_exists('acabatipresentacio', $this->data)) {
            $pres = $this->data['acabatipresentacio'];
            $pres = str_replace('#', "\n", $pres);
            $this->presentation = $pres;
        }
    }

    private function assign_id() {
        $id_line = strtok($this->str, "\n");
        $parts = explode('=', $id_line);
        $id = trim($parts[1]);
        if(!empty($id))
            $this->id = $id;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'cookbook' => $this->cookbook,
            'title' => $this->title,
            'temperature' => $this->temperature,
            'people ' => intval($this->people),
            'year' => intval($this->year),
            'season ' => $this->season,
            'category' => $this->category,
            'description ' => $this->description,
            'presentation ' => $this->presentation,
            'ingredients ' => $this->ingredients,
        ];
    }

    public function dump() {
        echo "\n========== BEGIN RECIPE TEXT ==========\n";
        echo $this->description;
        echo "\n========== BEGIN PRESENTATION ==========\n";
        echo $this->presentation;
        echo "\n=========== END RECIPE TEXT ===========\n";
    }

    public function toMarkdown() {
        $md = "# $this->title\n\n";
        $md .= "## Ingredients\n\n";
        foreach($this->ingredients as $in)
            $md .= '* ' . $in . "\n";
        $md .= "\n";
        $md .= "## Preparation\n\n";
        $md .= $this->description;
        $md .= "\n\n";
        $md .= $this->presentation;
        $md .= "\n";
        return $md;
    }
}

// Import

$params = [
    'cookbook' => 'elBulli1998-2002',
    'language' => 'uk',
];

$path = "/Volumes/{$params['cookbook']}/{$params['language']}/data/";
$contents = file_get_contents($path.'478.dat');

$recipe = new ElBulliRecipe($contents);
var_dump($recipe);

exit;

$directory = new DirectoryIterator($path);
$i = 455;

foreach($directory as $file) {
    if($i >= 825) break;

    if(!$file->isDot() && $file->getExtension() == 'dat') {
        echo "Reading file {$file->getPathname()}\r\n";
        $contents = file_get_contents($file->getPathname());
        $recipe = new ElBulliRecipe($contents, $params);
        var_dump($recipe->toArray());
        echo "\n";
    } else {
        echo "Skipping file {$file->getPathname()}\r\n";
    }
    $i++;
}
