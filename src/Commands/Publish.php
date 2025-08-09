<?php

declare(strict_types=1);

namespace Decoda\Audit\Commands;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Publisher\Publisher;
use Throwable;

class Publish extends AuditCommand
{
    protected $name        = 'audit:publish';
    protected $description = 'Publish Audit config file into the current application.';

    /**
     * @return void
     */
    public function run(array $params)
    {
        $source = service('autoloader')->getNamespace('Decoda\\Audit')[0];

        $publisher = new Publisher($source, APPPATH);

        try {
            $publisher->addPaths([
                'Config/Audit.php',
            ])->merge(false);
        } catch (Throwable $e) {
            $this->showError($e);

            return;
        }

        foreach ($publisher->getPublished() as $file) {
            $publisher->replace(
                $file,
                [
                    'namespace Decoda\\Audit\\Config' => 'namespace Config',
                    'use CodeIgniter\\Config\\BaseConfig;' => '',
                    'class Audit extends BaseConfig'       => 'class Audit extends \Decoda\\Audit\\Config\\Audit',
                ],
            );
        }

        CLI::write(CLI::color('  Published! ', 'green') . 'You can customize the configuration by editing the "app/Config/Audit.php" file.');
    }
}
