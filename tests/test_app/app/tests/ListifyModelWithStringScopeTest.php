<?php

use Way\Tests\Assert;

class ListifyModelWithStringScopeTest extends ListifyBaseTest {

    protected $model = 'FooWithStringScopeA';
    protected $modelScopeValue = "company = 'companyA'";

    private $modelB = 'FooWithStringScopeB';
    private $modelBScopeValue = "company = 'companyB'";

    public function setUp()
    {
        parent::setUp();

        //Allows model events to work during testing
        $modelB = $this->modelB;
        $modelB::boot();

        for($i = 1; $i <= 10; $i++)
        {
            $foo = App::make($this->modelB);
            $foo->name = $this->modelB . $i;
            $foo->company = 'companyB';
            $foo->save();
        }

        $this->reloadBFoos();
    }

    public function tearDown()
    {
        parent::tearDown();

        $modelB = $this->modelB;
        $modelB::flushEventListeners();
    }

    public function test_changeScopeBeforeUpdate()
    {
        $foo1 = $this->model;
        $foo1 = new $foo1();
        $foo1->name = $this->model . "Test1";
        $foo1->company = 'TestCompany1';
        $foo1->setListifyConfig('scope', "company = 'TestCompany1'");
        $foo1->save();

        $foo2 = $this->model;
        $foo2 = new $foo2();
        $foo2->name = $this->model . "Test2";
        $foo2->company = 'TestCompany1';
        $foo2->setListifyConfig('scope', "company = 'TestCompany1'");
        $foo2->save();

        Assert::eq(1, $foo1->getListifyPosition());
        Assert::eq(2, $foo2->getListifyPosition());

        $foo1->company = 'TestCompany2';
        $foo1->setListifyConfig('scope', "company = 'TestCompany2'");
        $foo1->save();

        Assert::eq(1, $foo1->getListifyPosition());
        Assert::eq(2, $foo2->getListifyPosition());
    }

    //The whole point of this is to validate that the secondary model (that shares the table) is not modified when manipulating the primary model. The scope should prevent that, so we validate that the secondary model has not changed after each test.
    protected function childAssertion()
    {
        $this->reloadBFoos();

        $position = 1;
        foreach($this->bfoos as $bfoo)
        {
            Assert::eq($position, $bfoo->getListifyPosition());
            $position++;
        }
    }

    protected function reloadFoos()
    {
        $this->foos = App::make($this->model)->whereRaw($this->modelScopeValue)->orderBy('id', "ASC")->get()->all();
    }

    private function reloadBFoos()
    {
        $this->bfoos = App::make($this->modelB)->whereRaw($this->modelBScopeValue)->orderBy('id', "ASC")->get()->all();
    }
}