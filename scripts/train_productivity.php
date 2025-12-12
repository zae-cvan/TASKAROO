<?php
require __DIR__ . '/../vendor/autoload.php';

use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;
use Rubix\ML\Classifiers\KNearestNeighbors;
use Rubix\ML\Datasets\Labeled;

// Path to store the model
$modelPath = __DIR__ . '/../storage/ml_models/productivity.model';
$persistor = new Filesystem($modelPath);

// Example classifier: K Nearest Neighbors
$estimator = new KNearestNeighbors(3);
$model = new PersistentModel($estimator, $persistor);

// Example training data
// Features: task completion percentage
// Labels: productivity level
$samples = [
    [20], [35], [50], [65], [80], [95]
];
$labels = [
    'Very Low', 'Low', 'Medium', 'High', 'Very High', 'Excellent'
];

// Wrap in a Labeled dataset
$dataset = new Labeled($samples, $labels);

// Train the model
$model->train($dataset);

// Save the model
$model->save();

echo "✅ Productivity model trained and saved at: $modelPath" . PHP_EOL;
