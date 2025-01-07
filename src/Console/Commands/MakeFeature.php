<?php

namespace Rudi97277\LaravelMakeFeature\Console\Commands;

use Illuminate\Console\Command;

class MakeFeature extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:feature {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new feature with controller, service, repository, and model';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /**
         *  Define the target types and their corresponding file suffixes.
         *  type => File suffix
         */
        $targets = [
            'controller' => 'Controller',
            'service' => 'Service',
            'repository' => 'Repository',
            'model' => '',
            'route' => 'Route'
        ];

        $defaultName = $this->argument('name');

        foreach ($targets as $k => $v) {
            $stub = file_get_contents(__DIR__ . "/../Stubs/$k.stub");

            $data = $this->prepareData("Features/$defaultName/", $k, $defaultName, $v);

            if (!$data) {
                continue;
            }

            array_unshift($data['additionalDir'], str_replace($v, '', $data['name']));
            $this->storeData($data, $stub);

            $this->info("$k $data[path] created successfully.");
        }
    }

    /**
     * Prepare the data for creating the file.
     *
     * This method splits the feature name into directories, determines the path for the new file,
     * and checks if the file already exists. If the file exists, it will return null to prevent overwriting.
     * If the directory doesn't exist, it will create it.
     *
     * @param string $appFolder The base folder for features (e.g., "Features").
     * @param string $type The type of the file being created (e.g., controller, service).
     * @param string $defaultName The full name of the feature (e.g., "Blog/Post").
     * @param string $suffix The suffix to append to the file (e.g., "Controller").
     * @return array|null The prepared data for the file creation or null if the file already exists.
     */
    public function prepareData(string $appFolder, string $type, string $defaultName, string $suffix = ''): ?array
    {
        $explodedName = array_filter(explode('/', $defaultName), fn($item) => $item != '');
        $name = end($explodedName);
        $additionalDir = array_slice($explodedName, 0, -1);

        $folder = \app_path($appFolder . implode('/', $additionalDir) . (count($additionalDir) > 0 ? '/' : ''));

        $path = $folder . end($explodedName) . "$suffix.php";

        if (file_exists($path)) {
            $this->error("$type already exists!");
            return null;
        }

        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        return [
            'path' => $path,
            'name' => $name,
            'arguments' => $defaultName,
            'additionalDir' => $additionalDir,
        ];
    }

    /**
     * Store the generated data in the appropriate file.
     *
     * This method takes the prepared data and the content from the stub file, replaces placeholders with
     * the actual values (e.g., the feature name), and writes the final content to the file.
     *
     * @param array $data The prepared data for the file.
     * @param string $stub The content of the stub file.
     * @param array $addKeySearch Additional placeholders to search for in the stub (optional).
     * @param array $addReplace Additional values to replace the placeholders with (optional).
     */
    public function storeData(array $data, string $stub, array $addKeySearch = [], array $addReplace = [])
    {
        $content = str_replace(
            array_merge(['{{name}}', '{{additionalDir}}'], $addKeySearch),
            array_merge([$data['name'], count($data['additionalDir']) > 0 ? '\\' . implode('\\', $data['additionalDir']) : ''], $addReplace),
            $stub
        );

        file_put_contents($data['path'], $content);
    }
}
