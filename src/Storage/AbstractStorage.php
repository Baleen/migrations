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

namespace Baleen\Migrations\Storage;

use Baleen\Migrations\Exception\StorageException;
use Baleen\Migrations\Version\Collection\Migrated;
use Baleen\Migrations\Version\VersionInterface;

/**
 * Class AbstractStorage.
 *
 * @author Gabriel Somoza <gabriel@strategery.io>
 */
abstract class AbstractStorage implements StorageInterface
{
    /**
     * Reads versions from the storage file.
     *
     * @return Migrated
     *
     * @throws StorageException
     */
    final public function fetchAll()
    {
        $collection = $this->doFetchAll();
        if (!is_object($collection) || !$collection instanceof Migrated) {
            throw new StorageException(sprintf(
                'Method AbstractStorage::doFetchAll() must return a "%s" collection. Got "%s" instead."',
                Migrated::class,
                is_object($collection) ? get_class($collection) : gettype($collection)
            ));
        }
        return $collection;
    }

    /**
     * @inheritdoc
     */
    final public function update(VersionInterface $version)
    {
        $result = null;
        if ($version->isMigrated()) {
            $result = $this->save($version);
        } else {
            $result = $this->delete($version);
        }
        return $result;
    }

    /**
     * @return VersionInterface[]|Migrated
     */
    abstract protected function doFetchAll();
}