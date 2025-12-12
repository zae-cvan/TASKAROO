<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Rubix\ML\PersistentModel;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Classifiers\KNearestNeighbors;
use Rubix\ML\Persisters\Filesystem;

class TrainProductivityModel extends Command
{
    protected $signature = 'train:productivity';
    protected $description = 'Train the productivity ML model';

    public function handle()
    {
        $this->info('Training productivity model...');

        // Sample training data: [task completion %, productivity level]
        $samples = [
            [20, 'Low'],
            [35, 'Low'],
            [50, 'Medium'],
            [65, 'Medium'],
            [80, 'High'],
            [90, 'High'],
        ];

        // Create labeled dataset
        $dataset = Labeled::fromIterator($samples);

        // Choose your classifier
        $estimator = new KNearestNeighbors(3);

        // Path to save model
        $modelPath = storage_path('ml_models/productivity.model');
        if (!file_exists(dirname($modelPath))) {
            mkdir(dirname($modelPath), 0777, true);
        }

        // Create persistent model
        $model = new PersistentModel($estimator, new Filesystem($modelPath));

        // Train model
        $model->train($dataset);

        $this->info("✅ Productivity model trained and saved at: $modelPath");
    }
}
