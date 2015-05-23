<?php

namespace CommerceGuys\Addressing\Tests\Validator\Constraints;

use CommerceGuys\Addressing\Validator\Constraints\Country as CountryConstraint;
use CommerceGuys\Addressing\Validator\Constraints\CountryValidator;
use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;

/**
 * @coversDefaultClass \CommerceGuys\Addressing\Validator\Constraints\CountryValidator
 */
class CountryValidatorTest extends AbstractConstraintValidatorTest
{
    /**
     * @var CountryConstraint
     */
    protected $constraint;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->constraint = new CountryConstraint();

        // The following code is copied from the parent setUp(), which isn't
        // called to avoid the call to \Locale, which introduces a dependency
        // on the intl extension (or symfony/intl).
        $this->group = 'MyGroup';
        $this->metadata = null;
        $this->object = null;
        $this->value = 'InvalidValue';
        $this->root = 'root';
        $this->propertyPath = '';
        $this->context = $this->createContext();
        $this->validator = $this->createValidator();
        $this->validator->initialize($this->context);
    }

    protected function getApiVersion()
    {
        // The equivalent of Validation::API_VERSION_2_5_BC, needed to trigger
        // BC mode in symfony/validator >= 2.5.
        return 3;
    }

    protected function createValidator()
    {
        return new CountryValidator();
    }

    /**
     * @covers \CommerceGuys\Addressing\Validator\Constraints\CountryValidator
     *
     * @uses \CommerceGuys\Addressing\Repository\AddressFormatRepository
     * @uses \CommerceGuys\Addressing\Repository\SubdivisionRepository
     */
    public function testEmptyIsValid()
    {
        $this->validator->validate(null, $this->constraint);
        $this->assertNoViolation();

        $this->validator->validate('', $this->constraint);
        $this->assertNoViolation();
    }

    /**
     * @covers \CommerceGuys\Addressing\Validator\Constraints\CountryValidator
     *
     * @uses \CommerceGuys\Addressing\Repository\AddressFormatRepository
     * @uses \CommerceGuys\Addressing\Repository\SubdivisionRepository
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testInvalidValueType()
    {
        $this->validator->validate(new \stdClass(), $this->constraint);
    }

    /**
     * @covers \CommerceGuys\Addressing\Validator\Constraints\CountryValidator
     *
     * @uses \CommerceGuys\Addressing\Provider\DataProvider
     * @uses \CommerceGuys\Addressing\Repository\AddressFormatRepository
     * @uses \CommerceGuys\Addressing\Repository\SubdivisionRepository
     */
    public function testInvalidCountry()
    {
        $this->validator->validate('InvalidValue', $this->constraint);
        $this->buildViolation($this->constraint->message)
            ->setParameters(['{{ value }}' => '"InvalidValue"'])
            ->atPath('')
            ->assertRaised();
    }

    /**
     * @covers \CommerceGuys\Addressing\Validator\Constraints\CountryValidator
     *
     * @uses \CommerceGuys\Addressing\Provider\DataProvider
     * @uses \CommerceGuys\Addressing\Repository\AddressFormatRepository
     * @uses \CommerceGuys\Addressing\Repository\SubdivisionRepository
     * @dataProvider getValidCountries
     */
    public function testValidCountries($country)
    {
        $this->validator->validate($country, $this->constraint);
        $this->assertNoViolation();
    }

    public function getValidCountries()
    {
        return [
            ['GB'],
            ['AT'],
            ['MY'],
        ];
    }
}
