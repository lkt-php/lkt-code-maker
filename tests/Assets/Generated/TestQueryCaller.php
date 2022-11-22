<?php namespace Lkt\CodeMaker\Tests\Assets\Generated;

use Lkt\Factory\Instantiator\Instantiator;
use Lkt\QueryCaller\QueryCaller;

class TestQueryCaller extends QueryCaller
{
    const COMPONENT = 'maker-test-component';

    public static function getCaller(): QueryCaller
    {
        /** @var QueryCaller $caller */
        list($caller) = Instantiator::getQueryCaller(static::COMPONENT);
        return $caller;
    }

    public function andIdEqual(int $value)
    {
        return $this->andIntegerEqual('id', $value);
    }

    public function andIdNot(int $value)
    {
        return $this->andIntegerNot('id', $value);
    }

    public function andIdGreaterThan(int $value)
    {
        return $this->andIntegerGreaterThan('id', $value);
    }

    public function andIdLowerThan(int $value)
    {
        return $this->andIntegerLowerThan('id', $value);
    }

    public function orIdEqual(int $value)
    {
        return $this->orIntegerEqual('id', $value);
    }

    public function orIdNot(int $value)
    {
        return $this->orIntegerNot('id', $value);
    }

    public function orIdGreaterThan(int $value)
    {
        return $this->orIntegerGreaterThan('id', $value);
    }

    public function orIdLowerThan(int $value)
    {
        return $this->orIntegerLowerThan('id', $value);
    }

    public function andNameEqual(string $value)
    {
        return $this->andStringEqual('name', $value);
    }

    public function andNameLike(string $value)
    {
        return $this->andStringLike('name', $value);
    }

    public function andNameIn(array $values)
    {
        return $this->andStringIn('name', $values);
    }

    public function andNameNotIn(array $values)
    {
        return $this->andStringNotIn('name', $values);
    }

    public function orNameEqual(string $value)
    {
        return $this->orStringEqual('name', $value);
    }

    public function orNameLike(string $value)
    {
        return $this->orStringLike('name', $value);
    }

    public function orNameIn(array $values)
    {
        return $this->orStringIn('name', $values);
    }

    public function orNameNotIn(array $values)
    {
        return $this->orStringNotIn('name', $values);
    }

    public function andDescriptionEqual(string $value)
    {
        return $this->andStringEqual('description', $value);
    }

    public function andDescriptionLike(string $value)
    {
        return $this->andStringLike('description', $value);
    }

    public function andDescriptionIn(array $values)
    {
        return $this->andStringIn('description', $values);
    }

    public function andDescriptionNotIn(array $values)
    {
        return $this->andStringNotIn('description', $values);
    }

    public function orDescriptionEqual(string $value)
    {
        return $this->orStringEqual('description', $value);
    }

    public function orDescriptionLike(string $value)
    {
        return $this->orStringLike('description', $value);
    }

    public function orDescriptionIn(array $values)
    {
        return $this->orStringIn('description', $values);
    }

    public function orDescriptionNotIn(array $values)
    {
        return $this->orStringNotIn('description', $values);
    }

    public function andAgeEqual(int $value)
    {
        return $this->andIntegerEqual('age', $value);
    }

    public function andAgeNot(int $value)
    {
        return $this->andIntegerNot('age', $value);
    }

    public function andAgeGreaterThan(int $value)
    {
        return $this->andIntegerGreaterThan('age', $value);
    }

    public function andAgeLowerThan(int $value)
    {
        return $this->andIntegerLowerThan('age', $value);
    }

    public function orAgeEqual(int $value)
    {
        return $this->orIntegerEqual('age', $value);
    }

    public function orAgeNot(int $value)
    {
        return $this->orIntegerNot('age', $value);
    }

    public function orAgeGreaterThan(int $value)
    {
        return $this->orIntegerGreaterThan('age', $value);
    }

    public function orAgeLowerThan(int $value)
    {
        return $this->orIntegerLowerThan('age', $value);
    }

    public function andEmailAddressEqual(string $value)
    {
        return $this->andStringEqual('email_address', $value);
    }

    public function andEmailAddressLike(string $value)
    {
        return $this->andStringLike('email_address', $value);
    }

    public function andEmailAddressIn(array $values)
    {
        return $this->andStringIn('email_address', $values);
    }

    public function andEmailAddressNotIn(array $values)
    {
        return $this->andStringNotIn('email_address', $values);
    }

    public function orEmailAddressEqual(string $value)
    {
        return $this->orStringEqual('email_address', $value);
    }

    public function orEmailAddressLike(string $value)
    {
        return $this->orStringLike('email_address', $value);
    }

    public function orEmailAddressIn(array $values)
    {
        return $this->orStringIn('email_address', $values);
    }

    public function orEmailAddressNotIn(array $values)
    {
        return $this->orStringNotIn('email_address', $values);
    }

    public function andIsReadyIsTrue()
    {
        return $this->andBooleanTrue('isReady');
    }

    public function andIsReadyIsFalse()
    {
        return $this->andBooleanFalse('isReady');
    }

    public function orIsReadyIsTrue()
    {
        return $this->orBooleanTrue('isReady');
    }

    public function orIsReadyIsFalse()
    {
        return $this->orBooleanFalse('isReady');
    }

    public function andPriceEqual(float $value)
    {
        return $this->andDecimalEqual('price', $value);
    }

    public function andPriceNot(float $value)
    {
        return $this->andDecimalNot('price', $value);
    }

    public function andPriceGreaterThan(float $value)
    {
        return $this->andDecimalGreaterThan('price', $value);
    }

    public function andPriceLowerThan(float $value)
    {
        return $this->andDecimalLowerThan('price', $value);
    }

    public function orPriceEqual(float $value)
    {
        return $this->orDecimalEqual('price', $value);
    }

    public function orPriceNot(float $value)
    {
        return $this->orDecimalNot('price', $value);
    }

    public function orPriceGreaterThan(float $value)
    {
        return $this->orDecimalGreaterThan('price', $value);
    }

    public function orPriceLowerThan(float $value)
    {
        return $this->orDecimalLowerThan('price', $value);
    }

    public function andMainPropertyEqual(int $value)
    {
        return $this->andIntegerEqual('mainProperty', $value);
    }

    public function andMainPropertyNot(int $value)
    {
        return $this->andIntegerNot('mainProperty', $value);
    }

    public function andMainPropertyGreaterThan(int $value)
    {
        return $this->andIntegerGreaterThan('mainProperty', $value);
    }

    public function andMainPropertyLowerThan(int $value)
    {
        return $this->andIntegerLowerThan('mainProperty', $value);
    }

    public function orMainPropertyEqual(int $value)
    {
        return $this->orIntegerEqual('mainProperty', $value);
    }

    public function orMainPropertyNot(int $value)
    {
        return $this->orIntegerNot('mainProperty', $value);
    }

    public function orMainPropertyGreaterThan(int $value)
    {
        return $this->orIntegerGreaterThan('mainProperty', $value);
    }

    public function orMainPropertyLowerThan(int $value)
    {
        return $this->orIntegerLowerThan('mainProperty', $value);
    }
}