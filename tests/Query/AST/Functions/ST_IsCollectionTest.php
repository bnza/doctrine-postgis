<?php

namespace Jsor\Doctrine\PostGIS\Query\AST\Functions;

use Jsor\Doctrine\PostGIS\AbstractFunctionalTestCase;
use Jsor\Doctrine\PostGIS\PointsEntity;

class ST_IsCollectionTest extends AbstractFunctionalTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->_setUpEntitySchema(array(
            'Jsor\Doctrine\PostGIS\PointsEntity'
        ));

        $em = $this->_getEntityManager();

        $entity = new PointsEntity(array(
            'text' => 'foo',
            'geometry' => 'POINT(1 1)',
            'point' => 'POINT(1 1)',
            'point2D' => 'SRID=3785;POINT(1 1)',
            'point3DZ' => 'SRID=3785;POINT(1 1 1)',
            'point3DM' => 'SRID=3785;POINTM(1 1 1)',
            'point4D' => 'SRID=3785;POINT(1 1 1 1)',
            'point2DNullable' => null,
            'point2DNoSrid' => 'POINT(1 1)',
            'geography' => 'SRID=4326;POINT(1 1)',
            'pointGeography2d' => 'SRID=4326;POINT(1 1)',
            'pointGeography2dSrid' => 'POINT(1 1)',
        ));

        $em->persist($entity);
        $em->flush();
        $em->clear();
    }

    public function testQuery1()
    {
        $query = $this->_getEntityManager()->createQuery('SELECT ST_IsCollection(ST_GeomFromText(\'LINESTRING(0 0, 1 1)\')) FROM Jsor\\Doctrine\\PostGIS\\PointsEntity');

        $result = $query->getSingleResult();

        // Convert possible binary stream resources
        array_walk_recursive($result, function (&$data) {
            if (is_resource($data)) {
                $data = stream_get_contents($data);

                if (false !== ($pos = strpos($data, 'x'))) {
                    $data = substr($data, $pos + 1);
                }
            }
        });

        $expected = array (
  1 => false,
);

        $this->assertEquals($expected, $result);
    }

    public function testQuery2()
    {
        $query = $this->_getEntityManager()->createQuery('SELECT ST_IsCollection(ST_GeomFromText(\'MULTIPOINT((0 0))\')) FROM Jsor\\Doctrine\\PostGIS\\PointsEntity');

        $result = $query->getSingleResult();

        // Convert possible binary stream resources
        array_walk_recursive($result, function (&$data) {
            if (is_resource($data)) {
                $data = stream_get_contents($data);

                if (false !== ($pos = strpos($data, 'x'))) {
                    $data = substr($data, $pos + 1);
                }
            }
        });

        $expected = array (
  1 => true,
);

        $this->assertEquals($expected, $result);
    }
}
