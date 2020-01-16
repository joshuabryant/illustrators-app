<?php

/**
 *
 */
function addIllustrator($illustrator, &$db)
{
    if (strlen(trim($illustrator['tags'])) > 0) {
        $tags = explode(',', $illustrator['tags']);
        foreach ($tags as &$tag) {
            $tag = strtolower(trim($tag));
        }
    } else {
        $tags = [];
    }

    $new = [
        'id' => time(),
        'name' => trim($illustrator['name']),
        'website' => trim($illustrator['website']),
        'tags' => $tags,
    ];

    $db['illustrators'][] = $new;

    sortIllustrators($db);

    close($db);
}


/**
 *
 */
function updateIllustrator($data, &$db)
{
    foreach ($db['illustrators'] as $index => &$illustrator) {
        if ($illustrator['id'] == $data['id']) {
            if (strlen(trim($data['tags'])) > 0) {
                $tags = explode(',', $data['tags']);
                foreach ($tags as &$tag) {
                    $tag = strtolower(trim($tag));
                }
            } else {
                $tags = [];
            }

            $illustrator = array_merge(
                $data,
                [
                    'name' => trim($data['name']),
                    'website' => trim($data['website']),
                    'tags' => $tags,
                ]
            );
        }
    }
    $db['illustrators'] = array_values($db['illustrators']);

    sortIllustrators($db);

    close($db);
}


/**
 *
 */
function removeIllustrator($id, &$db)
{
    foreach ($db['illustrators'] as $index => $illustrator) {
        if ($illustrator['id'] == $id) {
            unset($db['illustrators'][$index]);
        }
    }
    $db['illustrators'] = array_values($db['illustrators']);

    sortIllustrators($db);


    close($db);
}

/**
 *
 */
function sortIllustrators(&$db) {
    usort($db['illustrators'], function ($a, $b) {
        return $a['name'] < $b['name'] ? -1 : 1;
    });
}


/**
 *
 */
function addIllustratorImage($data, &$db)
{
    $id = $data['id'];

    foreach ($db['illustrators'] as $index => $illustrator) {
        if ($illustrator['id'] == $id) {
            $db['illustrators'][$index]['images'][] = $data['image'];
        }
    }

    close($db);
}


/**
 *
 */
function removeIllustratorImage($data, &$db)
{
    $id = $data['id'];
    $imageIdx = $data['imageIdx'];

    foreach ($db['illustrators'] as $index => $illustrator) {
        if ($illustrator['id'] == $id) {
            unset($db['illustrators'][$index]['images'][$imageIdx]);
            $db['illustrators'][$index]['images'] = array_values($db['illustrators'][$index]['images']);
        }
    }

    close($db);
}

/**
 *
 */
function setIllustratorThumb($data, &$db)
{
    $id = $data['id'];
    $imageIdx = $data['imageIdx'];

    foreach ($db['illustrators'] as $index => $illustrator) {
        if ($illustrator['id'] == $id) {
            $image = array_splice(
                $db['illustrators'][$index]['images'],
                $imageIdx,
                1
            );
            array_unshift($db['illustrators'][$index]['images'], $image[0]);
        }
    }

    close($db);
}

/**
 *
 */
function close(&$db)
{
    file_put_contents('storage/data.json', json_encode($db));

    header('Content-type: application/json');
    echo json_encode($db);
}




// DEFAULT PROCESS

// Get "database"
$db = json_decode(file_get_contents('storage/data.json'), true);

// Retrieve input
$data = json_decode(file_get_contents('php://input'), true);


// If we have an associated API action...
if (isset($data['action']) && function_exists($data['action'])) {
    // Make a backup
    file_put_contents('storage/backups/data-' . date('Y-m-d--His') . '.json', json_encode($db));

    // Call action
    $data['action']($data['data'], $db);
}
