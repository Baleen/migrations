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
 * <https://github.com/baleen/migrations>.
 */

namespace Baleen\Migrations\Event\Timeline;

use Baleen\Migrations\Event\EventInterface;
use Baleen\Migrations\Migration\Options;
use Baleen\Migrations\Version;
use Baleen\Migrations\Version\Collection\Linked;
use Baleen\Migrations\Version\VersionInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class CollectionEvent.
 *
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
class CollectionEvent extends Event implements EventInterface
{
    /**
     * @var Linked
     */
    protected $collection;

    /**
     * @var Options
     */
    protected $options;

    /**
     * @var Version
     */
    protected $target;

    /**
     * @var Progress
     */
    protected $progress;

    /**
     * CollectionEvent constructor.
     *
     * @param VersionInterface $target
     * @param Options $options
     * @param Linked $versions
     * @param Progress $progress
     */
    public function __construct(VersionInterface $target, Options $options, Linked $versions, Progress $progress = null)
    {
        $this->options = $options;
        $this->target = $target;
        $this->collection = $versions;
        $this->progress = $progress;
    }

    /**
     * @return Options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return Linked
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @return Version
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return Progress
     */
    public function getProgress()
    {
        return $this->progress;
    }
}
