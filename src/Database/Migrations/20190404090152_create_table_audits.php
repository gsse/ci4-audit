<?php

namespace Decoda\Audit\Database\Migrations;

use CodeIgniter\Database\Migration;

class Migration_create_table_audits extends Migration
{
    public function up()
    {
        // audit logs
        $fields = [
            'source'     => ['type' => 'varchar', 'constraint' => 63],
            'source_id'  => ['type' => 'binary', 'constraint' => 26],
            'company_id'  => ['type' => 'binary', 'constraint' => 26],
            'user_id'    => ['type' => 'binary', 'constraint' => 26, 'null' => true],
            'event'      => ['type' => 'varchar', 'constraint' => 32],
            'summary'    => ['type' => 'json'],
            'created_at' => ['type' => 'datetime', 'null' => true],
        ];

        $this->forge->addField('id');
        $this->forge->addField($fields);

        $this->forge->addKey(['source', 'source_id', 'event']);
        $this->forge->addKey(['user_id', 'source', 'event']);
        $this->forge->addKey(['event', 'user_id', 'source', 'source_id']);
        $this->forge->addKey('created_at');

        $this->forge->createTable('audits');
    }

    public function down()
    {
        $this->forge->dropTable('audits');
    }
}
