<?php

namespace Drupal\poll\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Answer controller.
 */
class AnswerController extends ControllerBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * Construct
   *
   * @param \Drupal\Core\Database\Connection $databaseConnection
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  /**
  * overview
   */
  public function overview() {

    $rows = [];

    $header = [
      [
        'data'  => $this->t('ID'),
        'field' => 'pa.id',
        'class' => [RESPONSIVE_PRIORITY_MEDIUM],
      ],
      [
        'data'  => $this->t('Name'),
        'field' => 'pa.name',
        'class' => [RESPONSIVE_PRIORITY_MEDIUM],
      ],
      [
        'data'  => $this->t('Operations'),
        'class' => [RESPONSIVE_PRIORITY_LOW],
      ],
    ];

    $query = $this->connection->select('poll_answer', 'pa')
      ->extend('\Drupal\Core\Database\Query\PagerSelectExtender')
      ->extend('\Drupal\Core\Database\Query\TableSortExtender');
    $query->fields('pa', [
      'id',
      'name'
    ]);
    
    $pollAnwers = $query
      ->limit(50)
      ->orderByHeader($header)
      ->execute();

    foreach ($pollAnwers as $pollAnswer) {
      $rows[] = [
        'data' => [
        	$pollAnswer->id,
          	$this->t($pollAnswer->name),
          	$this->l($this->t('Edit'), new Url('poll.answer.edit', ['id' => $pollAnswer->id])),
            $this->l($this->t('Delete'), new Url('poll.answer.delete', ['id' => $pollAnswer->id]))
        ]
      ];
    }

    $build['poll_answer_table'] = [
      '#type' 	=> 'table',
      '#header' => $header,
      '#rows' 	=> $rows,
      '#empty' 	=> $this->t('No answer available.'),
    ];
    $build['poll_answer_pager'] = ['#type' => 'pager'];

    return $build;
  }
}
