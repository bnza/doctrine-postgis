<?php

namespace Jsor\Doctrine\PostGIS\Query\AST\Functions;

use Jsor\Doctrine\PostGIS\AbstractFunctionalTestCase;
use Jsor\Doctrine\PostGIS\PointsEntity;

class ST_GeomFromGMLTest extends AbstractFunctionalTestCase
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
        $query = $this->_getEntityManager()->createQuery('SELECT ST_AsEWKT(ST_GeomFromGML(\'<gml:LineString srsName="EPSG:4269"><gml:coordinates>-71.16028,42.258729 -71.160837,42.259112 -71.161143,42.25932</gml:coordinates></gml:LineString>\')) FROM Jsor\\Doctrine\\PostGIS\\PointsEntity');

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
  1 => 'SRID=4269;LINESTRING(-71.16028 42.258729,-71.160837 42.259112,-71.161143 42.25932)',
);

        $this->assertEquals($expected, $result);
    }

    public function testQuery2()
    {
        $query = $this->_getEntityManager()->createQuery('SELECT ST_AsEWKT(ST_GeomFromGML(\'<gml:LineString><gml:coordinates>-71.16028,42.258729 -71.160837,42.259112 -71.161143,42.25932</gml:coordinates></gml:LineString>\', 4326)) FROM Jsor\\Doctrine\\PostGIS\\PointsEntity');

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
  1 => 'SRID=4326;LINESTRING(-71.16028 42.258729,-71.160837 42.259112,-71.161143 42.25932)',
);

        $this->assertEquals($expected, $result);
    }
}
