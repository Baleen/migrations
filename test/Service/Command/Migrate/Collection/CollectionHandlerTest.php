<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace BaleenTest\Migrations\Service\Command\Migrate\Collection;

use Baleen\Migrations\Migration\Options\Direction;
use Baleen\Migrations\Migration\OptionsInterface;
use Baleen\Migrations\Service\Command\Migrate\AbstractFactoryHandler;
use Baleen\Migrations\Service\Command\Migrate\Collection\CollectionHandler;
use Baleen\Migrations\Service\Runner\Factory\CollectionRunnerFactoryInterface;
use Baleen\Migrations\Service\Runner\RunnerInterface;
use Baleen\Migrations\Shared\Collection\CollectionInterface;
use Baleen\Migrations\Version\Comparator\ComparatorInterface;
use Baleen\Migrations\Version\VersionInterface;
use BaleenTest\Migrations\Service\Command\Migrate\HandlerTestCase;
use Mockery as m;

/**
 * Class CollectionHandlerTest
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
class CollectionHandlerTest extends HandlerTestCase
{
    /**
     * testConstructor
     * @return void
     */
    public function testConstructor()
    {
        $handler = $this->createHandler();
        $this->assertInstanceOf(AbstractFactoryHandler::class, $handler);
    }

    /**
     * testHandle
     * @return void
     */
    public function testHandle()
    {
        $handler = $this->createHandler();

        $command = CollectionCommandTest::createMockedCommand();

        /** @var OptionsInterface|m\Mock $options */
        $options= $command->getOptions();
        $options->shouldReceive('getDirection')
            ->once()
            ->withNoArgs()
            ->andReturn(Direction::down()); // using 'down' because its more declarative for the ->isDown test below

        /** @var CollectionInterface|m\Mock $filteredCollection */
        $filteredCollection = m::mock(CollectionInterface::class);

        /** @var ComparatorInterface|m\Mock $comparator */
        $comparator = m::mock(ComparatorInterface::class);
        $comparator->shouldReceive('withDirection')
            ->once()
            ->with(m::on(function (Direction $direction) {
                return $direction->isDown(); // test is forces the direction specified in options
            }))
            ->andReturnSelf();
        $comparator->shouldReceive('compare')
            ->with(m::type(VersionInterface::class), $command->getTarget())
            ->once()
            ->andReturn(0);

        /** @var CollectionInterface|m\Mock $collection */
        $collection = $command->getCollection();
        $collection->shouldReceive('getComparator')
            ->once()
            ->andReturn($comparator);
        $collection->shouldReceive('filter')
            ->with(m::on(function (callable $func) {
                /** @var VersionInterface|m\Mock $v */
                $v = m::mock(VersionInterface::class);
                $v->shouldReceive('isMigrated')->once()->withNoArgs()->andReturn(true);

                $res = $func($v);
                $this->assertTrue(is_bool($res));
                $this->assertTrue($res);

                return true;
            }))
            ->once()
            ->andReturnSelf();

        /** @var RunnerInterface|m\Mock $runner */
        $runner = $this->invokeMethod('createRunnerFor', $handler, [$collection]);
        $runner->shouldReceive('run')
            ->with(
                m::type(VersionInterface::class),
                m::type(OptionsInterface::class)
            )
            ->once()
            ->andReturn('foo');

        $collection->shouldReceive('sort')
            ->once()->with($comparator)->andReturn($filteredCollection);

        $result = $handler->handle($command);

        $this->assertSame('foo', $result);
    }

    /**
     * createHandler
     * @param RunnerInterface $runner
     * @return CollectionHandler
     */
    protected function createHandler(RunnerInterface $runner = null) {
        if (null === $runner) {
            $runner = $this->getRunnerMock();
        }
        /** @var CollectionRunnerFactoryInterface|m\Mock $factory */
        $factory = m::mock(CollectionRunnerFactoryInterface::class);
        $factory->shouldReceive('create')
            ->with(m::type(CollectionInterface::class))
            ->zeroOrMoreTimes()
            ->andReturn($runner);
        return new CollectionHandler($factory);
    }
}