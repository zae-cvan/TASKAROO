<?php

require __DIR__ . '/../vendor/autoload.php';

use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\Datasets\Unlabeled;

// Check if a task completion percentage is provided
if ($argc < 2) {
    echo "Usage: php predict_productivity.php <task_completion_percentage>" . PHP_EOL;
    exit(1);
}

$taskCompletion = (float)$argv[1];

// Path to the saved model
$modelPath = __DIR__ . '/../storage/ml_models/productivity.model';
$persistor = new Filesystem($modelPath);

// Load the trained model
$model = PersistentModel::load($persistor);

// Wrap the input in an Unlabeled dataset
$input = Unlabeled::fromIterator([[$taskCompletion]]);

// Make prediction
$prediction = $model->predict($input);

echo "Task completion: $taskCompletion% -> Predicted productivity level: {$prediction[0]}" . PHP_EOL;
