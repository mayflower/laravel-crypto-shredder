<?php

declare(strict_types=1);

namespace Unit;

use Illuminate\Encryption\Encrypter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mayflower\LaravelCryptoShredder\ShredderOptions;
use Mayflower\LaravelCryptoShredder\Tests\TestCase;
use Mayflower\LaravelCryptoShredder\Tests\TestClassNotModel;
use Mayflower\LaravelCryptoShredder\Tests\TestModel;
use Mayflower\LaravelCryptoShredder\Tests\TestModelInvalidAttribute;
use ReflectionClass;
use ReflectionMethod;

class UseCryptoShreddingTest extends TestCase
{
    use RefreshDatabase;

    private string $key;

    private Encrypter $encrypter;

    protected function setUp(): void
    {
        parent::setUp();
        $cypher = 'aes-128-cbc';
        $this->key = Encrypter::generateKey($cypher);
        $this->encrypter = new Encrypter($this->key, $cypher);
    }

    protected static function getMethod(string $name, string|null $className = null): ReflectionMethod
    {
        /** @var class-string $className */
        $className = $className ?? TestModel::class;
        $class = new ReflectionClass($className);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

    public function testItThrowsLogicExceptionWhenNotEloquentModel(): void
    {
        $this->expectException(\LogicException::class);

        new TestClassNotModel();
    }

    public function testItEncryptsOnCreate(): void
    {
        $testValue = 'this-is-a-test-string';

        $model = new TestModel();
        $model->setAttribute('someAttribute', $testValue);

        $method = self::getMethod('encryptOnCreate');
        $method->invokeArgs($model, [$this->key]);
        $result = $model->getAttribute('someAttribute');

        self::assertNotSame($testValue, $result);

        /** @var string $someAttribute */
        $someAttribute = $model->getAttribute('someAttribute');

        $result = $this->encrypter->decrypt($someAttribute);

        self::assertSame($testValue, $result);
    }

    public function testItEncryptsOnUpdate(): void
    {
        $testValue = 'this-is-a-test-string';

        $model = new TestModel();
        $model->setAttribute('someAttribute', $testValue);

        $method = self::getMethod('encryptOnUpdate');
        $method->invokeArgs($model, [$this->key]);
        $result = $model->getAttribute('someAttribute');

        self::assertNotSame($testValue, $result);

        /** @var string $someAttribute */
        $someAttribute = $model->getAttribute('someAttribute');

        $result = $this->encrypter->decrypt($someAttribute);

        self::assertSame($testValue, $result);
    }

    public function testItDecryptsOnRetrieve(): void
    {
        $testValue = 'this-is-a-test-string';

        $model = new TestModel();
        $model->setAttribute('someAttribute', $testValue);

        $method = self::getMethod('encryptOnCreate');
        $method->invokeArgs($model, [$this->key]);
        $result = $model->getAttribute('someAttribute');

        self::assertNotSame($testValue, $result);

        $method = self::getMethod('decryptOnRetrieve');
        $method->invokeArgs($model, [$this->key]);
        $result = $model->getAttribute('someAttribute');

        self::assertSame($testValue, $result);
    }

    public function testItSavesCryptoKey(): void
    {
        $key = Encrypter::generateKey('aes-128-cbc');
        $model = new TestModel();
        $model->setAttribute('id', 42);

        $method = self::getMethod('saveKey');
        $method->invokeArgs($model, [$model, $key]);

        $this->assertDatabaseHas('keys', [
            'key' => $key
        ]);
    }

    public function testItRetrievesCryptoKey(): void
    {
        $key = Encrypter::generateKey('aes-128-cbc');
        $model = new TestModel();
        $model->setAttribute('id', 42);

        $method = self::getMethod('saveKey');
        $method->invokeArgs($model, [$model, $key]);

        $method = self::getMethod('retrieveKey');
        /** @var string $result */
        $result = $method->invokeArgs($model, [$model]);

        self::assertSame($key, $result);
    }

    public function testItGetsShredderOptionsObject(): void
    {
        self::assertInstanceOf(ShredderOptions::class, TestModel::getShredderOptions());
    }

    public function testItThrowsInvalidArgumentExceptionOnInvalidAttributeOption(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $model = new TestModelInvalidAttribute();
        $method = self::getMethod('encryptOnCreate', TestModelInvalidAttribute::class);
        $method->invokeArgs($model, [$this->key]);
    }
}
