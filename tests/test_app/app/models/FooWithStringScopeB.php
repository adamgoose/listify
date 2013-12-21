<?php

/*
This is here because the files aren't being autoloaded by PHPUnit due to the scope of the testing.
If you have a better idea, I'm all ears! travis@lookitsatravis.com
 */

require_once __DIR__ . '/../../../../src/lookitsatravis/Listify/Listify.php';
require_once __DIR__ . '/../../../../src/lookitsatravis/Listify/Exceptions/ListifyException.php';

class FooWithStringScopeB extends Eloquent 
{
    use lookitsatravis\Listify\Listify;

    protected $table ="foo_with_string_scopes";

    /**
     * The fillable array lets laravel know which fields are fillable
     *
     * @var array
     */
    protected $fillable = ['name', 'company'];

    /**
     * The rules array lets us know how to to validate this model
     *
     * @var array
     */
    public $rules = [
        'name' => 'required',
        'company' => 'required'
    ];

    /**
     * __construct method
     * 
     * @param array   $attributes - An array of attributes to initialize the model with
     * @param boolean $exists     - Boolean flag to indicate if the model exists or not
     */
    public function __construct($attributes = array(), $exists = false)
    {    
        parent::__construct($attributes, $exists);
        $this->initListify([
            'scope' => "company = 'companyB'"
        ]);
    }

    public static function boot()
    {
        parent::boot();
        static::bootListify();
    }
}