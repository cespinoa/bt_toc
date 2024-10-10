<?php
namespace Drupal\bt_toc\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\bootstrap_toolbox\UtilityServiceInterface;


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
   * The utility service
   *
   * @var \Drupal\bootstrap_toolbox\UtilityServiceInterface
   */
  protected $utilityService;

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
   * @param \Drupal\bootstrap_toolbox\UtilityServiceInterface $utilityService
   *   The utility service
   */
  public function __construct(array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entityTypeManager,
    UtilityServiceInterface $utilityService
    ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
    $this->utilityService = $utilityService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('bootstrap_toolbox.utility_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'add_affix_control' => FALSE,
    ];
  }


  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    // Recupera la configuración actual.
    $config = $this->getConfiguration();

    $form['add_affix_control'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Add affix control'),
      '#default_value' => $config['add_affix_control'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    // Guarda el valor ingresado en el formulario.
    $this->setConfigurationValue('add_affix_control', $form_state->getValue('add_affix_control'));
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    /** @var \Drupal\Core\Routing\RouteMatchInterface $routeMatch */
    $routeMatch = $this->utilityService->getRouteMatch();
    $node = $routeMatch->getParameter('node');
    if ($node instanceof Node) {
      
      $nodeType = $node->bundle();
      $nodeType = $this->entityTypeManager->getStorage('node_type')->load($nodeType);
      

      if ($nodeType){
        $addToc = $nodeType->getThirdPartySetting('bootstrap_toolbox', 'add_toc', FALSE);
        if($node->hasField('override_node_settings') && $node->get('override_node_settings')->value){
          $addToc = $node->get('table_of_content')->value;
        }
        if($addToc == TRUE){
          $theme = $this->utilityService->getBehaviorSelectors();
          $bootstrapVersion = 5;

          if ($theme){
            $selectors = $this->utilityService->getThemeSelectors($theme);
            if ($selectors){
              $bootstrapVersion = ['bootstrap_version'];    
            }
          }
          
          if ($bootstrapVersion == 3){
            $markupStart = '<div id="list-toc"><ul role="tablist" class="nav">';
            $markupEnd = '</ul></div>';
            $openItemTag = '<li>';
            $closeItemTag = '</li>';
          }
          else {
            $markupStart = '<div id="list-toc" class="list-group">';
            $markupEnd = '</div>';
            $openItemTag = '';
            $closeItemTag = '';
          }
          
          $body = $node->get('body')->value;
          $dom = new \DOMDocument();
          @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $body); 
          $markup = '';
          $xpath = new \DOMXPath($dom);
          $elements = $xpath->query('//h1 | //h2 | //h3 | //h4');

          if ($elements){
            $counter = 1;
            foreach ($elements as $element) {
              if ($element instanceof \DOMElement){
                $markup .= $openItemTag .
                    '<a class="list-group-item list-group-item-action level-' .
                    $element->tagName . '" href="#list-item-' .
                    $counter . '">' .
                    $element->textContent .
                    '</a>' .
                    $closeItemTag;
                $counter++;
              }
            }
            $markup .= '<a class="list-group-item list-group-item-action level-h1" href="#body-area">' . $this->t('Back top') . '</a>';
            $markup = $markupStart . $markup . $markupEnd;
            if($counter > 2){
              return [
                '#markup' => Markup::create($markup),
              ];
            }
          }

          return [];
        }
        return [];

      }
      
    }
    return [];    
  }

  
}


