<?php

namespace Model;


class Audit extends Model
{
	/**
	 * @return array
	 */
	public function getAudits()
	{
		return $this->getDatasource()->getAudits();
	}
	
	
	/**
	 * @param int $id
	 * @return array
	 */
	public function getEventById($id)
	{
		foreach ($this->getDatasource()->getAudits() as $event) {
			if ($event->audit_event_id == (int)$id) {
				return $event->toArray();
			}
		}
	}
	
	
	/**
	 * @return array 
	 */
	public function getAuditEventTypes()
	{
		return array(
			'WP_LGO',
			'GL_RLP',
			'SR_STA',
			'BC_UPD',
			'GB_BCI',
			'GL_RLA',
			'GL_SLV',
			'GB_AVI',
			'GL_SGV',
			'BC_MOD',
			'GS_SCI',
			'GL_LEP',
			'GP_SRE',
			'GL_LEB',
			'BC_DIS',
			'GL_STR'
		);
	}
}
