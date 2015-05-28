<?php


class class_test_logicalDelete extends class_testbase {

    public function testLogicalDelete() {

        class_orm_base::setObjHandleLogicalDeletedGlobal(class_orm_deletedhandling_enum::INCLUDED());
        $intCountTotal = class_module_system_aspect::getObjectCount();

        class_orm_base::setObjHandleLogicalDeletedGlobal(class_orm_deletedhandling_enum::EXCLUDED());
        $intCountActive = class_module_system_aspect::getObjectCount();

        class_orm_base::setObjHandleLogicalDeletedGlobal(class_orm_deletedhandling_enum::EXCLUSIVE());
        $intCountDeleted = class_module_system_aspect::getObjectCount();

        class_orm_base::setObjHandleLogicalDeletedGlobal(class_orm_deletedhandling_enum::EXCLUDED());

        echo "Creating aspect\n";

        $objAspect1 = new class_module_system_aspect();
        $objAspect1->setStrName("Dummy");
        $objAspect1->updateObjectToDb();


        $objAspect = new class_module_system_aspect();
        $objAspect->setStrName("logical delete");
        $objAspect->updateObjectToDb();
        $strAspectId = $objAspect->getSystemid();

        $this->assertEquals($intCountActive+2, class_module_system_aspect::getObjectCount());

        $arrAspects = class_module_system_aspect::getObjectList();
        $arrAspects = array_filter($arrAspects, function(class_module_system_aspect $objAspect) use ($strAspectId) { return $objAspect->getSystemid() == $strAspectId; });

        $this->assertEquals(1, count($arrAspects));


        echo "Deleting logically\n";
        $this->assertEquals($objAspect->getIntRecordDeleted(), 0);
        $objAspect->deleteObject();
        $this->assertEquals($objAspect->getIntRecordDeleted(), 1);


        class_orm_base::setObjHandleLogicalDeletedGlobal(class_orm_deletedhandling_enum::EXCLUDED());
        echo "Loading non-deleted only\n";
        $this->assertEquals($intCountActive+1, class_module_system_aspect::getObjectCount());

        $arrAspects = class_module_system_aspect::getObjectList();
        $arrAspects = array_filter($arrAspects, function(class_module_system_aspect $objAspect) use ($strAspectId) { return $objAspect->getSystemid() == $strAspectId; });

        $this->assertEquals(0, count($arrAspects));

        echo "Loading deleted only\n";
        class_orm_base::setObjHandleLogicalDeletedGlobal(class_orm_deletedhandling_enum::EXCLUSIVE());

        $arrAspects = class_module_system_aspect::getObjectList();
        $arrAspects = array_filter($arrAspects, function(class_module_system_aspect $objAspect) use ($strAspectId) { return $objAspect->getSystemid() == $strAspectId; });

        $this->assertEquals($intCountDeleted+1, class_module_system_aspect::getObjectCount());
        $this->assertEquals(1, count($arrAspects));


        echo "Loading mixed deleted and non-deleted\n";
        class_orm_base::setObjHandleLogicalDeletedGlobal(class_orm_deletedhandling_enum::INCLUDED());

        $arrAspects = class_module_system_aspect::getObjectList();
        $arrAspects = array_filter($arrAspects, function(class_module_system_aspect $objAspect) use ($strAspectId) { return $objAspect->getSystemid() == $strAspectId; });

        $this->assertEquals($intCountTotal+2, class_module_system_aspect::getObjectCount());
        $this->assertEquals(1, count($arrAspects));





        echo "Deleting from database\n";
        $objAspect->deleteObjectFromDatabase();

        class_orm_base::setObjHandleLogicalDeletedGlobal(class_orm_deletedhandling_enum::EXCLUDED());
        echo "Loading non-deleted only\n";
        $this->assertEquals($intCountActive+1, class_module_system_aspect::getObjectCount());
        $arrAspects = class_module_system_aspect::getObjectList();
        $arrAspects = array_filter($arrAspects, function(class_module_system_aspect $objAspect) use ($strAspectId) { return $objAspect->getSystemid() == $strAspectId; });
        $this->assertEquals(0, count($arrAspects));

        echo "Loading deleted only\n";
        class_orm_base::setObjHandleLogicalDeletedGlobal(class_orm_deletedhandling_enum::EXCLUSIVE());
        $arrAspects = class_module_system_aspect::getObjectList();
        $arrAspects = array_filter($arrAspects, function(class_module_system_aspect $objAspect) use ($strAspectId) { return $objAspect->getSystemid() == $strAspectId; });
        $this->assertEquals($intCountDeleted, class_module_system_aspect::getObjectCount());
        $this->assertEquals(0, count($arrAspects));





        echo "Deleting dummy node directly\n";
        $objAspect1->deleteObjectFromDatabase();


        echo "Loading non-deleted only\n";
        class_orm_base::setObjHandleLogicalDeletedGlobal(class_orm_deletedhandling_enum::EXCLUDED());
        $this->assertEquals($intCountActive, class_module_system_aspect::getObjectCount());

        echo "Loading deleted only\n";
        class_orm_base::setObjHandleLogicalDeletedGlobal(class_orm_deletedhandling_enum::EXCLUSIVE());
        $this->assertEquals($intCountDeleted, class_module_system_aspect::getObjectCount());

    }

}

