<?php

namespace BestInvestments\Tests\Research\Domain\Aggregates\Collections;

use BestInvestments\Research\Domain\Aggregates\Collections\ConsultationList;
use BestInvestments\Research\Domain\Entities\Consultation;
use BestInvestments\Research\Domain\ValueObjects\ConsultationIdentifier;
use BestInvestments\Research\Domain\ValueObjects\SpecialistIdentifier;
use BestInvestments\Tests\PrivatePropertyTrait;
use DateTimeImmutable;
use Illuminate\Support\Collection;

class ConsultationListTest extends \PHPUnit_Framework_TestCase
{
    use PrivatePropertyTrait;

    public function testAddPushesConsultationToCollection()
    {
        // Arrange
        $consultationList   = new ConsultationList();
        $consultation       = $this->getConsultation();

        // Act
        $consultationList->add($consultation);

        // Assert
        /** @var Collection $collection */
        $collection = $this->getInnerPropertyValueByReflection($consultationList, 'collection');

        $this->assertSame(1, $collection->count());
        $this->assertSame($collection->pop(), $consultation);
    }

    public function testHasOpenConsultationsReturnsTrueWhenThereIsAnOpenConsultation()
    {
        // Arrange
        $collection   = new ConsultationList();
        $consultation = $this->getConsultation();

        // Act
        $collection->add($consultation);

        // Assert
        $this->assertTrue($collection->hasOpenConsultations());
    }

    public function testHasOpenConsultationsReturnsFalseWhenThereIsNotAnOpenConsultation()
    {
        // Arrange
        $collection   = new ConsultationList();
        $consultation = $this->getConsultation();

        // Act
        $collection->add($consultation);
        $consultation->discard();

        // Assert
        $this->assertFalse($collection->hasOpenConsultations());
    }

    public function testGetOpenConsultationForSpecialistReturnsCorrectConsultation()
    {
        // Arrange
        $expected   = $this->getConsultation();
        $collection = new ConsultationList();
        $collection->add($expected);

        // Act
        $actual = $collection->getOpenConsultationForSpecialist(new SpecialistIdentifier('specialist-1234'));

        // Assert
        $this->assertSame($expected, $actual);
    }

    public function testGetOpenConsultationForSpecialistReturnsNullWhenConsultationIsClosed()
    {
        // Arrange
        $consultation = $this->getConsultation();
        $consultation->discard();

        $collection = new ConsultationList();
        $collection->add($consultation);

        // Act
        $result = $collection->getOpenConsultationForSpecialist(new SpecialistIdentifier('specialist-1234'));

        // Assert
        $this->assertNull($result);
    }

    public function testGetOpenConsultationForSpecialistReturnsNullWhenSpecialistDoesNotMatch()
    {
        // Arrange
        $consultation = $this->getConsultation();
        $collection   = new ConsultationList();
        $collection->add($consultation);

        // Act
        $result = $collection->getOpenConsultationForSpecialist(new SpecialistIdentifier('specialist-9876'));

        // Assert
        $this->assertNull($result);
    }

    public function testGet()
    {
        // Arrange
        $expected       = $this->getConsultation();
        $consultationId = $expected->getId();

        $consultationList = new ConsultationList();
        $consultationList->add($expected);

        // Act
        $actual = $consultationList->get($consultationId);

        // Assert
        $this->assertSame($expected, $actual);
    }

    public function testGetReturnsNullIfThereIsNoConsultationWithTheId()
    {
        // Arrange
        $consultationList = new ConsultationList();
        $consultationList->add($this->getConsultation());

        // Act
        $result = $consultationList->get(new ConsultationIdentifier('consultation-5432'));

        // Assert
        $this->assertNull($result);
    }

    private function getConsultation(): Consultation
    {
        return new Consultation(
            new DateTimeImmutable('2016-12-12'),
            new SpecialistIdentifier('specialist-1234')
        );
    }
}
