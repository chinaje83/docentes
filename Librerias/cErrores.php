<?php 
class cErrores
{
    protected $sType;
    protected $sErrFile;
    protected $iErrLine;
    protected $sErrMsg;
    protected $bContext;
    
    /**
     * @desc constructor
     *
     * @param constant $cErrno
     * @param string $sErrStr
     * @param string $sErrFile
     * @param int $iErrLine
     * @param mixed $mVars
     * @param boolean $bContext
     */
    public function __construct($cErrno, $sErrStr, $sErrFile, $iErrLine, $mVars, $bContext = false) {
        $this->sErrFile = $sErrFile;
        $this->sErrFile = $sErrFile;
        $this->iErrLine = $iErrLine;
        $this->mVars = $mVars;
        $this->bContext = $bContext;
    }
 


	public function getMsgError()
	{
		return $this->mErrMsg;	
	}



}
?>