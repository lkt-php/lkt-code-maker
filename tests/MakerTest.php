<?php

namespace Lkt\CodeMaker\Tests;

use Lkt\CodeMaker\CodeMaker;
use Lkt\CodeMaker\Tests\Assets\TestClass;
use Lkt\Factory\Instantiator\Conversions\RawResultsToInstanceConverter;
use Lkt\Factory\Schemas\Fields\BooleanField;
use Lkt\Factory\Schemas\Fields\ColorField;
use Lkt\Factory\Schemas\Fields\DateTimeField;
use Lkt\Factory\Schemas\Fields\EmailField;
use Lkt\Factory\Schemas\Fields\FileField;
use Lkt\Factory\Schemas\Fields\FloatField;
use Lkt\Factory\Schemas\Fields\ForeignKeyField;
use Lkt\Factory\Schemas\Fields\ForeignKeysField;
use Lkt\Factory\Schemas\Fields\HTMLField;
use Lkt\Factory\Schemas\Fields\IdField;
use Lkt\Factory\Schemas\Fields\ImageField;
use Lkt\Factory\Schemas\Fields\IntegerField;
use Lkt\Factory\Schemas\Fields\JSONField;
use Lkt\Factory\Schemas\Fields\PivotField;
use Lkt\Factory\Schemas\Fields\RelatedField;
use Lkt\Factory\Schemas\Fields\RelatedKeysField;
use Lkt\Factory\Schemas\Fields\StringField;
use Lkt\Factory\Schemas\Fields\UnixTimeStampField;
use Lkt\Factory\Schemas\InstanceSettings;
use Lkt\Factory\Schemas\Schema;
use PHPUnit\Framework\TestCase;

class MakerTest extends TestCase
{
    /**
     * @return void
     */
    public function testTemplateEngine()
    {
        $component = 'maker-test-component';
        Schema::add(
            Schema::table('test-table', $component)
                ->setInstanceSettings(InstanceSettings::define(TestClass::class)
                    ->setClassNameForGeneratedClass('GeneratedTestClass')
                    ->setNamespaceForGeneratedClass('Lkt\CodeMaker\Tests\Assets\Generated')
                    ->setWhereStoreGeneratedClass(__DIR__ . '/Assets/Generated')
                )
            ->addField(IdField::define('id'))
            ->addField(StringField::define('name'))
            ->addField(HTMLField::define('description'))
            ->addField(IntegerField::define('age'))
            ->addField(EmailField::define('emailAddress'))
            ->addField(BooleanField::define('isReady'))
            ->addField(FloatField::define('price'))
            ->addField(DateTimeField::define('createdAt'))
            ->addField(UnixTimeStampField::define('moment'))
            ->addField(ForeignKeysField::define('properties')->setComponent('maker-test-component'))
            ->addField(ForeignKeyField::define('mainProperty')->setComponent('maker-test-component'))
            ->addField(RelatedField::define('relatedData')->setComponent('maker-test-component'))
            ->addField(RelatedKeysField::define('relatedKeysData')->setComponent('maker-test-component'))
            ->addField(PivotField::define('pivotData')->setComponent('maker-test-component'))
            ->addField(FileField::define('file'))
            ->addField(ImageField::define('image')->setPublicPath('/api/file'))
            ->addField(ColorField::define('favouriteColor'))
            ->addField(JSONField::define('settings'))
            ->addField(JSONField::define('additionalSettings')->setIsAssoc())
        );


        CodeMaker::generate();

        $settings = new \StdClass();
        $settings->env = 'dev';

        $instance = TestClass::getInstance(555, $component, (new RawResultsToInstanceConverter($component, [
            'id' => 555,
            'price' => 27.8222222,
            'name' => 'Lorem',
            'description' => 'Ipsum',
            'file' => 'sample.pdf',
            'moment' => time(),
            'emailAddress' => 'a@a.a',
            'favouriteColor' => '#ffff00',
            'isReady' => true,
            'settings' => $settings,
            'additionalSettings' => [
                'env' => 'dev'
            ],
        ]))->parse());

        // Check default values
        $this->assertEquals(555, $instance->getId());
        $this->assertEquals('Lorem', $instance->getName());
        $this->assertEquals('Ipsum', $instance->getDescription());
        $this->assertEquals('sample.pdf', $instance->getFileName());
        $this->assertEquals('a@a.a', $instance->getEmailAddress());
        $this->assertEquals(27.8222222, $instance->getPrice());
        $this->assertEquals(27.822, $instance->getPriceFormatted());
        $this->assertEquals('#ffff00', $instance->getFavouriteColor());
        $this->assertEquals([255, 255, 0], $instance->getFavouriteColorRgb());
        $this->assertEquals('rgb(255,255,0)', $instance->getFavouriteColorRgbFormatted());
        $this->assertTrue($instance->isReady());

        $this->assertEquals(date('Y-m-d'), $instance->getMomentFormatted('Y-m-d'));
        $this->assertEquals('dev', $instance->getSettings()->env);
        $this->assertEquals('dev', $instance->getAdditionalSettings()['env']);
        $this->assertEquals('dev', $instance->getAdditionalSettings()['env']);
//        $this->assertEquals(date('Y-m-d'), $instance->getMoment());
//        dd($instance);

//        $this->assertTrue(false);
    }
}