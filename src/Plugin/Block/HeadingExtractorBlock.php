<?php
namespace Drupal\bt_toc\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Entity\EntityTypeManagerInterface;


/**
 * Provides a 'Table of content' Block.
 *
 * @Block(
 *   id = "bt_toc_block",
 *   admin_label = @Translation("BT TOC"),
 * )
 */
class HeadingExtractorBlock extends BlockBase  implements ContainerFactoryPluginInterface{

  

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new HeadingExtractorBlock object.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entityTypeManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager') // Obtén el servicio entity_type.manager.
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof Node) {
      
      $nodeType = $node->bundle();
      $nodeType = $this->entityTypeManager->getStorage('node_type')->load($nodeType);
      $addToc = $nodeType->getThirdPartySetting('bootstrap_toolbox', 'add_toc', FALSE);
      if($node->hasField('override_node_settings') && $node->get('override_node_settings')->value){
        $addToc = $node->get('table_of_content')->value;
      }
      if($addToc == TRUE){
        
        $body = $node->get('body')->value;
        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $body); // Suppress errors with @.
        $markup = '<div id="list-toc" class="list-group">';

        $xpath = new \DOMXPath($dom);
        $elements = $xpath->query('//h1 | //h2 | //h3 | //h4');

        $counter = 1;
        foreach ($elements as $element) {
          $markup .= '<a class="list-group-item list-group-item-action level-' . $element->tagName . '" href="#list-item-' . $counter . '">' . $element->textContent . '</a>';
          $counter++;
        }
        $markup .= '<a class="list-group-item list-group-item-action level-h1" href="#body-area">' . $this->t('Back top') . '</a>';
        
        $markup .= '</div>';
        if($counter > 2){
          return [
            '#markup' => Markup::create($markup),
          ];
        }
        return NULL;
      }
      return NULL;
      
    }
    return NULL;    
  }

  
}


