<?php

/**
 * @property TestUtils_Mixin_Connect $connection
 * @property TestUtils_Mixin_Files $files
 *
 * @method void addLocationStrategy()
 * @method void addSelection()
 * @method void addSelectionAndWait()
 * @method void allowNativeXpath()
 * @method void altKeyDown()
 * @method void altKeyDownAndWait()
 * @method void altKeyUp()
 * @method void altKeyUpAndWait()
 * @method void answerOnNextPrompt()
 * @method void assignId()
 * @method void attachFile()
 * @method void break()
 * @method void captureEntirePageScreenshot()
 * @method void captureEntirePageScreenshotToString()
 * @method void captureScreenshot()
 * @method void captureScreenshotToString()
 * @method void check()
 * @method void chooseCancelOnNextConfirmation()
 * @method void chooseOkOnNextConfirmation()
 * @method void click()
 * @method void clickAndWait()
 * @method void clickAt()
 * @method void clickAtAndWait()
 * @method void close()
 * @method void contextMenu()
 * @method void contextMenuAndWait()
 * @method void contextMenuAt()
 * @method void contextMenuAtAndWait()
 * @method void controlKeyDown()
 * @method void controlKeyDownAndWait()
 * @method void controlKeyUp()
 * @method void controlKeyUpAndWait()
 * @method void createCookie()
 * @method void createCookieAndWait()
 * @method void deleteAllVisibleCookies()
 * @method void deleteAllVisibleCookiesAndWait()
 * @method void deleteCookie()
 * @method void deleteCookieAndWait()
 * @method void doubleClick()
 * @method void doubleClickAndWait()
 * @method void doubleClickAt()
 * @method void doubleClickAtAndWait()
 * @method void dragAndDrop()
 * @method void dragAndDropAndWait()
 * @method void dragAndDropToObject()
 * @method void dragAndDropToObjectAndWait()
 * @method void dragDrop()
 * @method void dragDropAndWait()
 * @method void echo()
 * @method void fireEvent()
 * @method void fireEventAndWait()
 * @method void focus()
 * @method string getAlert()
 * @method array getAllButtons()
 * @method array getAllFields()
 * @method array getAllLinks()
 * @method array getAllWindowIds()
 * @method array getAllWindowNames()
 * @method array getAllWindowTitles()
 * @method string getAttribute()
 * @method array getAttributeFromAllWindows()
 * @method string getBodyText()
 * @method string getConfirmation()
 * @method string getCookie()
 * @method string getCookieByName()
 * @method integer getCursorPosition()
 * @method integer getElementHeight()
 * @method integer getElementIndex()
 * @method integer getElementPositionLeft()
 * @method integer getElementPositionTop()
 * @method integer getElementWidth()
 * @method string getEval()
 * @method string getExpression()
 * @method string getHtmlSource()
 * @method string getLocation()
 * @method string getLogMessages()
 * @method integer getMouseSpeed()
 * @method string getPrompt()
 * @method array getSelectOptions()
 * @method string getSelectedId()
 * @method array getSelectedIds()
 * @method string getSelectedIndex()
 * @method array getSelectedIndexes()
 * @method string getSelectedLabel()
 * @method array getSelectedLabels()
 * @method string getSelectedValue()
 * @method array getSelectedValues()
 * @method void getSpeed()
 * @method void getSpeedAndWait()
 * @method string getTable()
 * @method string getText()
 * @method string getTitle()
 * @method string getValue()
 * @method boolean getWhetherThisFrameMatchFrameExpression()
 * @method boolean getWhetherThisWindowMatchWindowExpression()
 * @method integer getXpathCount()
 * @method void goBack()
 * @method void goBackAndWait()
 * @method void highlight()
 * @method void highlightAndWait()
 * @method void ignoreAttributesWithoutValue()
 * @method boolean isAlertPresent()
 * @method boolean isChecked()
 * @method boolean isConfirmationPresent()
 * @method boolean isCookiePresent()
 * @method boolean isEditable()
 * @method boolean isElementPresent()
 * @method boolean isOrdered()
 * @method boolean isPromptPresent()
 * @method boolean isSomethingSelected()
 * @method boolean isTextPresent()
 * @method boolean isVisible()
 * @method void keyDown()
 * @method void keyDownAndWait()
 * @method void keyDownNative()
 * @method void keyDownNativeAndWait()
 * @method void keyPress()
 * @method void keyPressAndWait()
 * @method void keyPressNative()
 * @method void keyPressNativeAndWait()
 * @method void keyUp()
 * @method void keyUpAndWait()
 * @method void keyUpNative()
 * @method void keyUpNativeAndWait()
 * @method void metaKeyDown()
 * @method void metaKeyDownAndWait()
 * @method void metaKeyUp()
 * @method void metaKeyUpAndWait()
 * @method void mouseDown()
 * @method void mouseDownAndWait()
 * @method void mouseDownAt()
 * @method void mouseDownAtAndWait()
 * @method void mouseMove()
 * @method void mouseMoveAndWait()
 * @method void mouseMoveAt()
 * @method void mouseMoveAtAndWait()
 * @method void mouseOut()
 * @method void mouseOutAndWait()
 * @method void mouseOver()
 * @method void mouseOverAndWait()
 * @method void mouseUp()
 * @method void mouseUpAndWait()
 * @method void mouseUpAt()
 * @method void mouseUpAtAndWait()
 * @method void mouseUpRight()
 * @method void mouseUpRightAndWait()
 * @method void mouseUpRightAt()
 * @method void mouseUpRightAtAndWait()
 * @method void open()
 * @method void openWindow()
 * @method void openWindowAndWait()
 * @method void pause()
 * @method void refresh()
 * @method void refreshAndWait()
 * @method void removeAllSelections()
 * @method void removeAllSelectionsAndWait()
 * @method void removeSelection()
 * @method void removeSelectionAndWait()
 * @method void retrieveLastRemoteControlLogs()
 * @method void runScript()
 * @method void rollup()
 * @method void select()
 * @method void selectAndWait()
 * @method void selectFrame()
 * @method void selectWindow()
 * @method void setBrowserLogLevel()
 * @method void setContext()
 * @method void setCursorPosition()
 * @method void setCursorPositionAndWait()
 * @method void setMouseSpeed()
 * @method void setMouseSpeedAndWait()
 * @method void setSpeed()
 * @method void setSpeedAndWait()
 * @method void shiftKeyDown()
 * @method void shiftKeyDownAndWait()
 * @method void shiftKeyUp()
 * @method void shiftKeyUpAndWait()
 * @method void shutDownSeleniumServer()
 * @method void store()
 * @method void storeAlert()
 * @method void storeAlertPresent()
 * @method void storeAllButtons()
 * @method void storeAllFields()
 * @method void storeAllLinks()
 * @method void storeAllWindowIds()
 * @method void storeAllWindowNames()
 * @method void storeAllWindowTitle()s
 * @method void storeAttribute()
 * @method void storeAttributeFromAllWindows()
 * @method void storeBodyText()
 * @method void storeChecked()
 * @method void storeConfirmation()
 * @method void storeConfirmationPresent()
 * @method void storeCookie()
 * @method void storeCookieByName()
 * @method void storeCookiePresent()
 * @method void storeCursorPosition()
 * @method void storeEditable()
 * @method void storeElementHeight()
 * @method void storeElementIndex()
 * @method void storeElementPositionLeft()
 * @method void storeElementPositionTop()
 * @method void storeElementPresent()
 * @method void storeElementWidth()
 * @method void storeEval()
 * @method void storeExpression()
 * @method void storeHtmlSource()
 * @method void storeLocation()
 * @method void storeMouseSpeed()
 * @method void storeOrdered()
 * @method void storePrompt()
 * @method void storePromptPresent()
 * @method void storeSelectOptions()
 * @method void storeSelectedId()
 * @method void storeSelectedIds()
 * @method void storeSelectedIndex()
 * @method void storeSelectedIndexes()
 * @method void storeSelectedLabel()
 * @method void storeSelectedLabels()
 * @method void storeSelectedValue()
 * @method void storeSelectedValues()
 * @method void storeSomethingSelected()
 * @method void storeSpeed()
 * @method void storeTable()
 * @method void storeText()
 * @method void storeTextPresent()
 * @method void storeTitle()
 * @method void storeValue()
 * @method void storeVisible()
 * @method void storeWhetherThisFrameMatchFrameExpression()
 * @method void storeWhetherThisWindowMatchWindowExpression()
 * @method void storeXpathCount()
 * @method void submit()
 * @method void submitAndWait()
 * @method void type()
 * @method void typeAndWait()
 * @method void typeKeys()
 * @method void typeKeysAndWait()
 * @method void uncheck()
 * @method void uncheckAndWait()
 * @method void waitForCondition()
 * @method void waitForPageToLoad()
 * @method void waitForPopUp()
 * @method void windowFocus()
 * @method void windowMaximize()
 */
class TestUtils_WebTest extends PHPUnit_Extensions_SeleniumTestCase {

	/**
	 * @var string
	 */
	protected $pageUrl = '';

	/**
	 * @var Application
	 */
	protected $application;

	protected function screenshot($suffix = null, $screen = false) {
		$folder         = TESTS . DIRECTORY_SEPARATOR . 'screenshots' . DIRECTORY_SEPARATOR;
		$screenFileName = $folder . 'screen_' . get_class($this) . '_' . $this->getName(false);
		$windowFileName = $folder . get_class($this) . '_' . $this->getName(false);
		if ($suffix) {
			$screenFileName .= '_' . $suffix;
			$windowFileName .= '_' . $suffix;
		}
		$screenFileName .= '.png';
		$windowFileName .= '.png';
		$this->captureEntirePageScreenshot($windowFileName);
		if ($screen) {
			$this->captureScreenshot($screenFileName);
		}
	}

	protected function setUp() {
		if (!defined('SELENIUM_ENABLE')) {
			$this->markTestSkipped('Selenium disabled');
		}
		if (!isSet($GLOBALS['application'])) {
			$this->markTestSkipped('Store tested application instance in $GLOBALS array');
		}

		$this->application = $GLOBALS['application'];

		$this->addMixin('files', 'TestUtils_Mixin_Files');
		$this->addMixin('connection', 'TestUtils_Mixin_Connect');
		$this->checkConnection();

		$this->coverageScriptUrl = $this->url('/');
		$this->setUpData();

		$this->setBrowserUrl($this->url('/'));
		$this->start();

		$this->windowMaximize();
		$this->open($this->url('/'));
		$this->deleteAllVisibleCookies();
		$this->createCookie('PHPUNIT_SELENIUM_TEST_ID=' . $this->testId, 'path=/');
		$this->openPage();
	}

	protected function setUpData() {}

	/**
	 * @return string
	 * @param string $path
	 *
	 */
	protected function url($path) {
		return 'http://' . $this->application->config->get('web')->domain . $this->application->config->get('web')->url . $path;
	}

	/**
	 * @return void
	 */
	protected function openPage() {
		if ($this->pageUrl) {
			$this->openAndWait($this->url($this->pageUrl));
		}
	}

	/**
	 * @return void
	 * @param string $expected
	 * @param string $message
	 */
	protected function assertTitleEquals($expected, $message = '') {
		self::assertEquals($expected, $this->getTitle(), $message);
	}

	/**
	 * @return void
	 * @param string $expected
	 * @param string $message
	 */
	protected function assertLocation($expected, $message = '') {
		self::assertEquals('http://' . $this->application->config->get('web')->domain . $expected, $this->getLocation(), $message = '');
	}

	/**
	 * @return void
	 * @param int $timeout
	 */
	protected function waitForJQueryAjax($timeout = 5000) {
		$this->waitForCondition('selenium.browserbot.getCurrentWindow().jQuery.active == 0;', $timeout);
	}

	/**
	 * @return void
	 * @param string $selector
	 * @param string $value
	 */
	protected function setValue($selector, $value) {
		$this->type('css=' . $selector, $value);
	}

	protected function addMixin($property, $className) {
		if (isset($this->$property)) {
			throw new InvalidArgumentException('$property');
		}

		$class = new ReflectionClass($className);
		if (!$class->isSubclassOf('TestUtils_Mixin')) {
			throw new InvalidArgumentException('$className');
		}
		if (!$class->isInstantiable()) {
			throw new InvalidArgumentException('$className');
		}

		$this->$property = $class->newInstance();
	}

	protected function checkConnection() {
		$this->connection->check(self::$browsers[0]['host'], self::$browsers[0]['port'], 'Selenium RC not running on %s:%d.');
	}

}