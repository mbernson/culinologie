<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\DB;
use DirectoryIterator;

use ElBulliRecipe;
use ElBulliIngredient;

require 'script/parser.php';

class ImportRecipes extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'recipes:import';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{

            $params = [
                'cookbook' => 'elBulli1998-2002',
                'language' => 'cs',
            ];

            $path = "/Volumes/{$params['cookbook']}/{$params['language']}/data/";
            $directory = new DirectoryIterator($path);
            $this->insertAll($directory, $params);
            /* $this->insertSingle($path, '478', $params); */
	}

        private function insertSingle($path, $number, $params) {
            $contents = file_get_contents($path.$number.'.dat');

            $recipe = new ElBulliRecipe($contents, $params);
            var_dump($recipe);
            /* $recipe->insert(); */
        }

        private function insertAll(DirectoryIterator $directory, $params) {
            foreach($directory as $file) {
                if(!$file->isDot() && $file->getExtension() == 'dat' && is_numeric($file->getBaseName('.dat'))) {
                    echo "Reading file {$file->getPathname()}\n";
                    $contents = file_get_contents($file->getPathname());
                    $recipe = new ElBulliRecipe($contents, $params);
                    $recipe->insert();
                    /* var_dump($recipe->toArray()); */
                } else {
                    echo "Skipping file {$file->getPathname()}\n";
                }
            }
        }

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			/* ['example', InputArgument::REQUIRED, 'An example argument.'], */
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

}
