<?php

namespace Tests\Unit;

use App\Models\Recipe;
use Tests\TestCase;

class RecipeTest extends TestCase
{
    private Recipe $recipe;

    public function setUp(): void
    {
        parent::setUp();
        $this->recipe = new Recipe();
    }

    public function testParsingIngredients()
    {
        $ingredients = "1.5 theelepels olie\n30 kilo aardappels\nZout\n1,6g poeder\n  willekeurig\n";
        $parsed = Recipe::parseIngredientsFromText($ingredients);

        $first = $parsed[0];
        $this->assertEquals($first->text, '1.5 theelepels olie');
        $this->assertEquals($first->amount, '1.5');
        $this->assertEquals($first->unit, 'theelepels');

        $second = $parsed[1];
        $this->assertEquals($second->text, '30 kilo aardappels');
        $this->assertEquals($second->amount, '30');
        $this->assertEquals($second->unit, 'kilo');

        $third = $parsed[2];
        $this->assertEquals($third->text, 'Zout');
        $this->assertNull($third->amount);
        $this->assertNull($third->unit);

        $fourth = $parsed[3];
        $this->assertEquals($fourth->text, '1,6g poeder');
        $this->assertEquals($fourth->amount, '1,6');
        $this->assertEquals($fourth->unit, 'g');
    }

    public function testParsingIngredientsHeaders()
    {
        $ingredients = "1.5 theelepels olie\n## Voor de hoofdmoot\n30 kilo aardappels\nZout\n##Afwerking\n1,6g poeder\n  willekeurig\n";
        $parsed = Recipe::parseIngredientsFromText($ingredients);

        $this->assertNull($parsed[0]->header);

        $this->assertEquals('Voor de hoofdmoot', $parsed[1]->header);
        $this->assertEquals('Voor de hoofdmoot', $parsed[2]->header);

        $this->assertEquals('Afwerking', $parsed[4]->header);
        $this->assertEquals('Afwerking', $parsed[5]->header);
    }
}
