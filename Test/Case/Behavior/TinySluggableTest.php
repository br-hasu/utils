<?php

/**
 * TinySluggableArticle model used for tests
 */
class TinySluggableArticle extends CakeTestModel {

/**
 * Table
 *
 * @var string
 * @access public
 */
	public $useTable = 'articles';

/**
 * Behaviors
 *
 * @var array
 * @access public
 */
	public $actsAs = array('Utils.TinySluggable');

}

/**
 * TinySluggable Test Behavior
 **/
class TinySluggableBehaviorTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 * @access public
 */
	public $fixtures = array('plugin.Utils.Article');

/**
 * 
 *
 * @return void
 * @access public
 */
	public function setUp() {
		$this->Model = ClassRegistry::init('TinySluggableArticle');
		$this->Model->Behaviors->attach('Utils.TinySluggable', array());
	}

/**
 * 
 *
 * @return void
 * @access public
 */
	public function tearDown() {
		unset($this->Model);
		ClassRegistry::flush(); 
	}

/**
 * testBeforeSave
 *
 * @return void
 * @access public
 */
	public function testBeforeSave() {
		$this->Model->data = array(
			'TinySluggableArticle' => array(
				'title' => 'another title'));
		$this->assertTrue($this->Model->Behaviors->TinySluggable->beforeSave($this->Model));
		$this->assertEqual($this->Model->data['TinySluggableArticle']['tiny_slug'], '3');
	}

/**
 * testCustomConfig
 *
 * @return void
 * @access public
 */
	public function testCustomConfig() {
		$this->Model->Behaviors->detach('TinySluggable');
		$this->Model->Behaviors->attach('TinySluggable', array(
			'tinySlug' => 'tiny_slug',
			'codeset' => '2abcdefg'));

		$this->Model->data = array(
			'TinySluggableArticle' => array(
				'title' => 'and another title'));

		$this->assertTrue($this->Model->Behaviors->TinySluggable->beforeSave($this->Model));
		$this->assertTrue(!empty($this->Model->data['TinySluggableArticle']['tiny_slug']));
		$this->assertEqual($this->Model->data['TinySluggableArticle']['tiny_slug'], 'a');
	}

/**
 * testCustomConfig
 *
 * @return void
 * @access public
 */
	public function testFirstSlugUsingStdCodeset() {
		$this->Model->query('truncate table test_suite_articles');

		$result = $this->Model->save(array(
			'TinySluggableArticle' => array(
				'title' => 'and another title')));

		$this->assertEqual($result['TinySluggableArticle']['tiny_slug'], '0');
	}

/**
 * testCustomConfig
 *
 * @return void
 * @access public
 */
	public function testManySlugs() {
		$this->Model->query('truncate table test_suite_articles');
		$codeset = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		for ($i = 0; $i <= 25; $i++) {
			$expect[$i] = $codeset[$i];

			$this->Model->create();
			$this->Model->save(array(
						'TinySluggableArticle' => array(
							'title' => 'Another Title ' . $i)));
		}

		$results = Set::extract($this->Model->find('all'), '{n}.TinySluggableArticle.tiny_slug');
		$this->assertEqual($results, $expect);
	}
}
?>