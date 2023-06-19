<?php

namespace Drupal\ista_migration\Form;

use Drupal;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\file\Entity\FileInterface;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;


/**
 * Provides  ista magration form.
 */
class MigrationForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ista_magration_magration';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {


    // get all type node
    $type = [
      'page' => 'Page',
      'contact_us' => 'contact_us',
      'work_with_us' => 'work_with_us',
      'news' => 'news',
      'content' => 'content',
      'product' => 'product',
      'article' => 'article',
      'vendor' => 'vendor',
      'catalog' => 'catalog',
      'gallery' => 'gallery',
    ];

    $form['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Type'),
      '#options' => $type,
      '#required' => TRUE,
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send'),
    ];

    return $form;
  }


  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // get type from form
    $type = $form_state->getValue('type');
    if($type == 'gallery'){
      $this->gallery();
    }

  }


  public function gallery() {
    // Get the entity type manager service.
    $entityTypeManager = Drupal::entityTypeManager();

    // Get the node storage.
    $nodeStorage = $entityTypeManager->getStorage('node');

    $path = '../page/json-news.json';

    $jsonString = file_get_contents($path);
    $nodes = json_decode($jsonString, TRUE);
    foreach ($nodes as $node) {

      $nid = $node['Nid'];
      $title = $node['title'];
      $Language = $node['Language'];
      $lan = '';
      if ($Language == 'English') {
        $lan = 'en';
      }
      elseif ($Language == 'Persian') {
        $lan = 'fa';
      }


      // Create a query to fetch nodes.
      $query = $nodeStorage->getQuery()
        ->condition('status', 1)
        ->condition('type', 'gallery')
        ->condition('title', $title, '=')
        ->accessCheck(FALSE);

      // Execute the query and get the entity IDs.
      $entity_ids = $query->execute();

      // filter value is key is $selectId from $entity_ids

      $finalId = array_filter($entity_ids, function ($value, $key) use ($nid) {
        return $key == $nid;
      }, ARRAY_FILTER_USE_BOTH);

      $nodeEdite = $nodeStorage->load(reset($finalId));

      if (isset($node['field_image'])) {

        // get file from url
        $url = $node['field_image']['src'];
        $fid = $this->saveFile($url, $lan);
        try {
          // chech if isset $nodeEdite
          if ($nodeEdite) {

            if ($lan) {
              if ($nodeEdite->hasTranslation($lan)) {
                $nodeEdite->getTranslation($lan)->set('field_image', [
                  'target_id' => $fid,
                ])->save();
              }

              else {
                $nodeEdite->set('field_image', [
                  'target_id' => $fid,
                ])->save();
              }
            }
            else {
              $nodeEdite->set('field_image', [
                'target_id' => $fid,
              ])->save();
            }
          }
        } catch (Exception $e) {
          Drupal::logger('ista_magration')->error($e->getMessage());
        }
      }
      if (isset($node['field_gallery'])) {
        try {
          $items = $node['field_gallery'];
          $fids = [];
          foreach ($items as $item) {
            $url = $item['src'];
            $fid = $this->saveFile($url, $lan);
            $fids[] = $fid;
          }
          if ($lan) {
            if ($nodeEdite->hasTranslation($lan)) {
              $nodeEdite->getTranslation($lan)
                ->set('field_gallery', $fids)
                ->save();
            }
            else {
              $nodeEdite->set('field_gallery', $fids)->save();
            }
          }
          else {
            $nodeEdite->set('field_gallery', $fids)->save();
          }
        } catch (Exception $e) {
          Drupal::logger('ista_magration')->error($e->getMessage());
        }
      }
    }

  }

  public function catalog() {
    // Get the entity type manager service.
    $entityTypeManager = Drupal::entityTypeManager();

    // Get the node storage.
    $nodeStorage = $entityTypeManager->getStorage('node');

    $path = '../page/json-catalog.json';

    $jsonString = file_get_contents($path);
    $nodes = json_decode($jsonString, TRUE);
    foreach ($nodes as $node) {

      $nid = $node['Nid'];
      $title = $node['title'];
      $Language = $node['Language'];
      $lan = '';
      if ($Language == 'English') {
        $lan = 'en';
      }
      elseif ($Language == 'Persian') {
        $lan = 'fa';
      }


      // Create a query to fetch nodes.
      $query = $nodeStorage->getQuery()
        ->condition('status', 1)
        ->condition('type', 'catalog')
        ->condition('title', $title, '=')
        ->accessCheck(FALSE);

      // Execute the query and get the entity IDs.
      $entity_ids = $query->execute();

      // filter value is key is $selectId from $entity_ids

      $finalId = array_filter($entity_ids, function ($value, $key) use ($nid) {
        return $key == $nid;
      }, ARRAY_FILTER_USE_BOTH);
      $nodeEdite = $nodeStorage->load(reset($finalId));

      if (isset($node['field_image'])) {

        // get file from url
        $url = $node['field_image']['src'];
        $fid = $this->saveFile($url, $lan);
        try {
          // chech if isset $nodeEdite
          if ($nodeEdite) {

            if ($lan) {
              if ($nodeEdite->hasTranslation($lan)) {
                $nodeEdite->getTranslation($lan)->set('field_image', [
                  'target_id' => $fid,
                ])->save();
              }

              else {
                $nodeEdite->set('field_image', [
                  'target_id' => $fid,
                ])->save();
              }
            }
            else {
              $nodeEdite->set('field_image', [
                'target_id' => $fid,
              ])->save();
            }
          }
        } catch (Exception $e) {
          Drupal::logger('ista_magration')->error($e->getMessage());
        }
      }
      if (isset($node['field_background'])) {

        // get file from url
        $url = $node['field_background']['src'];
        $fid = $this->saveFile($url, $lan);
        try {
          // chech if isset $nodeEdite
          if ($nodeEdite) {

            if ($lan) {
              if ($nodeEdite->hasTranslation($lan)) {
                $nodeEdite->getTranslation($lan)->set('field_background', [
                  'target_id' => $fid,
                ])->save();
              }

              else {
                $nodeEdite->set('field_background', [
                  'target_id' => $fid,
                ])->save();
              }
            }
            else {
              $nodeEdite->set('field_background', [
                'target_id' => $fid,
              ])->save();
            }
          }
        } catch (Exception $e) {
          Drupal::logger('ista_magration')->error($e->getMessage());
        }
      }
    }

  }

  public function vendor() {
    // Get the entity type manager service.
    $entityTypeManager = Drupal::entityTypeManager();

    // Get the node storage.
    $nodeStorage = $entityTypeManager->getStorage('node');

    $path = '../page/json-vendor.json';

    $jsonString = file_get_contents($path);
    $nodes = json_decode($jsonString, TRUE);
    foreach ($nodes as $node) {

      $nid = $node['Nid'];
      $title = $node['title'];
      $Language = $node['Language'];
      $lan = '';
      if ($Language == 'English') {
        $lan = 'en';
      }
      elseif ($Language == 'Persian') {
        $lan = 'fa';
      }


      // Create a query to fetch nodes.
      $query = $nodeStorage->getQuery()
        ->condition('status', 1)
        ->condition('type', 'vendor')
        ->condition('title', $title, '=')
        ->accessCheck(FALSE);

      // Execute the query and get the entity IDs.
      $entity_ids = $query->execute();

      // filter value is key is $selectId from $entity_ids

      $finalId = array_filter($entity_ids, function ($value, $key) use ($nid) {
        return $key == $nid;
      }, ARRAY_FILTER_USE_BOTH);
      $nodeEdite = $nodeStorage->load(reset($finalId));

      if (isset($node['field_image'])) {

        // get file from url
        $url = $node['field_image']['src'];
        $fid = $this->saveFile($url, $lan);
        try {
          // chech if isset $nodeEdite
          if ($nodeEdite) {

            if ($lan) {
              if ($nodeEdite->hasTranslation($lan)) {
                $nodeEdite->getTranslation($lan)->set('field_image', [
                  'target_id' => $fid,
                ])->save();
              }

              else {
                $nodeEdite->set('field_image', [
                  'target_id' => $fid,
                ])->save();
              }
            }
            else {
              $nodeEdite->set('field_image', [
                'target_id' => $fid,
              ])->save();
            }
          }
        } catch (Exception $e) {
          Drupal::logger('ista_magration')->error($e->getMessage());
        }
      }
      if (isset($node['field_logo'])) {

        // get file from url
        $url = $node['field_logo']['src'];
        $fid = $this->saveFile($url, $lan);
        try {
          // chech if isset $nodeEdite
          if ($nodeEdite) {

            if ($lan) {
              if ($nodeEdite->hasTranslation($lan)) {
                $nodeEdite->getTranslation($lan)->set('field_logo', [
                  'target_id' => $fid,
                ])->save();
              }

              else {
                $nodeEdite->set('field_logo', [
                  'target_id' => $fid,
                ])->save();
              }
            }
            else {
              $nodeEdite->set('field_logo', [
                'target_id' => $fid,
              ])->save();
            }
          }
        } catch (Exception $e) {
          Drupal::logger('ista_magration')->error($e->getMessage());
        }
      }
    }

  }


  public function article() {
    // Get the entity type manager service.
    $entityTypeManager = Drupal::entityTypeManager();

    // Get the node storage.
    $nodeStorage = $entityTypeManager->getStorage('node');

    $path = '../page/json-article.json';

    $jsonString = file_get_contents($path);
    $nodes = json_decode($jsonString, TRUE);
    foreach ($nodes as $node) {

      $nid = $node['Nid'];
      $title = $node['title'];
      $Language = $node['Language'];
      $lan = '';
      if ($Language == 'English') {
        $lan = 'en';
      }
      elseif ($Language == 'Persian') {
        $lan = 'fa';
      }


      // Create a query to fetch nodes.
      $query = $nodeStorage->getQuery()
        ->condition('status', 1)
        ->condition('type', 'article')
        ->condition('title', $title, '=')
        ->accessCheck(FALSE);

      // Execute the query and get the entity IDs.
      $entity_ids = $query->execute();

      // filter value is key is $selectId from $entity_ids

      $finalId = array_filter($entity_ids, function ($value, $key) use ($nid) {
        return $key == $nid;
      }, ARRAY_FILTER_USE_BOTH);
      $nodeEdite = $nodeStorage->load(reset($finalId));

      if (isset($node['field_image'])) {

        // get file from url
        $url = $node['field_image']['src'];
        $fid = $this->saveFile($url, $lan);
        try {
          // chech if isset $nodeEdite
          if ($nodeEdite) {

            if ($lan) {
              if ($nodeEdite->hasTranslation($lan)) {
                $nodeEdite->getTranslation($lan)->set('field_image', [
                  'target_id' => $fid,
                ])->save();
              }

              else {
                $nodeEdite->set('field_image', [
                  'target_id' => $fid,
                ])->save();
              }
            }
            else {
              $nodeEdite->set('field_image', [
                'target_id' => $fid,
              ])->save();
            }
          }
        } catch (Exception $e) {
          Drupal::logger('ista_magration')->error($e->getMessage());
        }
      }
    }
  }


  public function product() {
    // Get the entity type manager service.
    $entityTypeManager = Drupal::entityTypeManager();

    // Get the node storage.
    $nodeStorage = $entityTypeManager->getStorage('node');

    $path = '../page/json-product.json';

    $jsonString = file_get_contents($path);
    $nodes = json_decode($jsonString, TRUE);
    foreach ($nodes as $node) {

      $nid = $node['Nid'];
      $title = $node['title'];
      $Language = $node['Language'];
      $lan = '';
      if ($Language == 'English') {
        $lan = 'en';
      }
      elseif ($Language == 'Persian') {
        $lan = 'fa';
      }


      // Create a query to fetch nodes.
      $query = $nodeStorage->getQuery()
        ->condition('status', 1)
        ->condition('type', 'product')
        ->condition('title', $title, '=')
        ->accessCheck(FALSE);

      // Execute the query and get the entity IDs.
      $entity_ids = $query->execute();

      // filter value is key is $selectId from $entity_ids

      $finalId = array_filter($entity_ids, function ($value, $key) use ($nid) {
        return $key == $nid;
      }, ARRAY_FILTER_USE_BOTH);
      $nodeEdite = $nodeStorage->load(reset($finalId));

      if (isset($node['field_image'])) {

        // get file from url
        $url = $node['field_image']['src'];
        $fid = $this->saveFile($url, $lan);
        try {
          // chech if isset $nodeEdite
          if ($nodeEdite) {

            if ($lan) {
              if ($nodeEdite->hasTranslation($lan)) {
                $nodeEdite->getTranslation($lan)->set('field_image', [
                  'target_id' => $fid,
                ])->save();
              }

              else {
                $nodeEdite->set('field_image', [
                  'target_id' => $fid,
                ])->save();
              }
            }
            else {
              $nodeEdite->set('field_image', [
                'target_id' => $fid,
              ])->save();
            }
          }
        } catch (Exception $e) {
          Drupal::logger('ista_magration')->error($e->getMessage());
        }
      }
      if (isset($node['field_list_image'])) {
        $url = $node['field_list_image']['src'];
        $fid = $this->saveFile($url, $lan);
        try {
          // chech if isset $nodeEdite
          if ($nodeEdite) {

            if ($lan) {
              if ($nodeEdite->hasTranslation($lan)) {
                $nodeEdite->getTranslation($lan)->set('field_list_image', [
                  'target_id' => $fid,
                ])->save();
              }

              else {
                $nodeEdite->set('field_list_image', [
                  'target_id' => $fid,
                ])->save();
              }
            }
            else {
              $nodeEdite->set('field_list_image', [
                'target_id' => $fid,
              ])->save();
            }
          }
        } catch (Exception $e) {
          Drupal::logger('ista_magration')->error($e->getMessage());
        }

      }
    }die();

  }


  public function news() {
    // Get the entity type manager service.
    $entityTypeManager = Drupal::entityTypeManager();

    // Get the node storage.
    $nodeStorage = $entityTypeManager->getStorage('node');

    $path = '../page/json-news.json';

    $jsonString = file_get_contents($path);
    $nodes = json_decode($jsonString, TRUE);
    foreach ($nodes as $node) {

      $nid = $node['Nid'];
      $title = $node['title'];
      $Language = $node['Language'];
      $lan = '';
      if ($Language == 'English') {
        $lan = 'en';
      }
      elseif ($Language == 'Persian') {
        $lan = 'fa';
      }


      // Create a query to fetch nodes.
      $query = $nodeStorage->getQuery()
        ->condition('status', 1)
        ->condition('type', 'news')
        ->condition('title', $title, '=')
        ->accessCheck(FALSE);

      // Execute the query and get the entity IDs.
      $entity_ids = $query->execute();

      // filter value is key is $selectId from $entity_ids

      $finalId = array_filter($entity_ids, function ($value, $key) use ($nid) {
        return $key == $nid;
      }, ARRAY_FILTER_USE_BOTH);
      $nodeEdite = $nodeStorage->load(reset($finalId));

      if (isset($node['field_image'])) {

        // get file from url
        $url = $node['field_image']['src'];
        $fid = $this->saveFile($url, $lan);
        try {
          // chech if isset $nodeEdite
          if ($nodeEdite) {

            if ($lan) {
              if ($nodeEdite->hasTranslation($lan)) {
                $nodeEdite->getTranslation($lan)->set('field_image', [
                  'target_id' => $fid,
                ])->save();
              }

              else {
                $nodeEdite->set('field_image', [
                  'target_id' => $fid,
                ])->save();
              }
            }
            else {
              $nodeEdite->set('field_image', [
                'target_id' => $fid,
              ])->save();
            }
          }
        } catch (Exception $e) {
          Drupal::logger('ista_magration')->error($e->getMessage());
        }
      }
      if (isset($node['field_gallery'])) {
        try {
          $items = $node['field_gallery'];
          $fids = [];
          foreach ($items as $item) {
            $url = $item['src'];
            $fid = $this->saveFile($url, $lan);
            $fids[] = $fid;
          }
          if ($lan) {
            if ($nodeEdite->hasTranslation($lan)) {
              $nodeEdite->getTranslation($lan)
                ->set('field_gallery', $fids)
                ->save();
            }
            else {
              $nodeEdite->set('field_gallery', $fids)->save();
            }
          }
          else {
            $nodeEdite->set('field_gallery', $fids)->save();
          }
        } catch (Exception $e) {
          Drupal::logger('ista_magration')->error($e->getMessage());
        }
      }
    }

  }

  public function saveFile($imageUrl, $lan) {


    // Fetch the image data from the URL.
    $imageData = file_get_contents($imageUrl);


    // Get the file name from the URL.
    $fileName = basename($imageUrl);

    // Prepare the directory for saving the file.
    $directory = 'public://' . $lan . '/';
    $destination = $directory . $fileName;
    $fileDirectory = 'public://images/';

    // Save the image data to the file.
    $fileSystem = Drupal::service('file_system');
    $fileSystem->prepareDirectory($fileDirectory, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS);
    $fileUri = $fileDirectory . $fileName;
    $fileSystem->saveData($imageData, $fileUri, FileSystemInterface::EXISTS_REPLACE);

    $fullPath = $fileSystem->realpath($fileUri);


    $file = File::create([
      'filename' => basename($fullPath),
      'uri' => 'public://images/' . basename($fullPath),
      'status' => 1,
      'uid' => 1,
    ]);
    $file->save();

    return $file->id();


  }


}
