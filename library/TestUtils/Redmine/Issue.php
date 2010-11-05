<?php

require LIB . '/vendor/ActiveResource.php';

class TestUtils_Redmine_Issue extends ActiveResource {

	const UNKNOWN_STATUS = 'unknown_status';
	const UNKNOWN_TICKET = 'unknown_ticket';

	public $request_format = 'xml';
	public $element_name   = 'issue';

	private static $statusMap = array(
		  'Новая'          => 'new'
		, 'Назначена'      => 'assigned'
		, 'В работе'       => 'assigned'
		, 'Приостановлена' => 'closed'
		, 'На проверку'    => 'closed'
		, 'Обратная связь' => 'reopened'
		, 'Закрыта'        => 'closed'
		, 'Отклонена'      => 'closed'
		, 'Отложена'       => 'closed'
	);

	private static $statusIds = array(
		  'new'      => 1
		, 'reopened' => 4
		, 'closed'   => 5
	);

	public function __construct(array $data = array()) {
		$config = Nano::config('redmine');
		$this->site         = $config->site;
		$this->user         = $config->user;
		$this->password     = $config->password;
		$data['project_id'] = $config->project_id;

		parent::__construct($data);
	}

	public function getStatus() {
		return (string)($this->status->attributes()->name);
	}

	public static function getIssue($id) {
		$result = new self();
		$result->find($id);
		return $result;
	}

	public static function sendReport($ticketId, $newStatus, $message, PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $failure = null) {
		$issue    = new self(array('id' => $ticketId));
		$class    = new ReflectionObject($test);
		$source   = (isset(Nano::config('redmine')->source) ? Nano::config('redmine')->source : '');
		$fileName = $source . subStr($class->getFileName(), strLen(ROOT) + 1);
		$testName = null;
		if ($test instanceof PHPUnit_Framework_TestCase) { /** @var $test PHPUnit_Framework_TestCase */
			$line     = $class->getMethod($test->getName())->getStartLine();
			$testName = '@"' . get_class($test) . '::' . $test->getName() . '":/projects/' . $issue->project_id .'/repository/entry/' . $fileName . '#L' . $line .'@';
		}
		$comment = $message
			. PHP_EOL . PHP_EOL . 'File name: source:' . $fileName
			. (null === $testName ? '' : PHP_EOL . 'Test: ' . $testName)
		;
		if ('closed' == $newStatus) {
		} else {
			$comment .=
				  PHP_EOL . 'Message: @' . $failure->toString() . '@'
				. PHP_EOL . '<pre>'
					. PHP_EOL . 'Trace: ' . PHP_EOL . $failure->getTraceAsString()
				. PHP_EOL . '</pre>'
			;
		}

		$issue
			->set('status_id', self::getStatusId($newStatus))
			->set('done_ratio', 'closed' == $newStatus ? 100 : 80)
			->set('notes', 'cdata:' . $comment)
		;

		try {
			$issue->save();
		} catch (Exception $e) {
		}
	}

	public static function convertStatus($object) {
		$status = $object instanceof TestUtils_Redmine_Issue ? $object->getStatus() : $object;
		if (isset(self::$statusMap[$status])) {
			return self::$statusMap[$status];
		}
		return self::UNKNOWN_STATUS;
	}

	public static function getStatusId($object) {
		$status = $object instanceof TestUtils_Redmine_Issue ? $object->getStatus() : $object;
		if (isset(self::$statusIds[$status])) {
			return self::$statusIds[$status];
		}
		return 0;
	}

}