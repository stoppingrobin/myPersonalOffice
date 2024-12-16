import DialogDataDto    from "../../../../DTO/DialogDataDto";
import DialogLogic      from "./DialogLogic";

/**
 * @description this class is responsible for loading dialog logic
 */
export default class DialogLogicLoader {

    /**
     * @description returns ModalDataDto for given dialog definition by it's name
     *              if no definition is present - returns null
     *
     * @param dialogName
     */
    public getDialogDataDto(dialogName: string): DialogDataDto|null
    {
        /**
         * @see DialogLogic.myIssueCardAddRecords()
         * @see DialogLogic.myIssueCardPreviewAndEdit()
         * @see DialogLogic.addOrModifyTodo()
         * @see DialogLogic.firstLoginInformation()
         * @see DialogLogic.tagsUpdate()
         * @see DialogLogic.updateIssue()
         * @see DialogLogic.massActionDataTransferImagesModule()
         * @see DialogLogic.massActionDataTransferVideoModule()
         * @see DialogLogic.massActionFilesRemoval()
         * @see DialogLogic.moveFolder()
         */
        let dialogDataBuilder = DialogLogic[dialogName];

        if( !$.isFunction(dialogDataBuilder) ){
            return null;
        }

        let dialogDataDto = dialogDataBuilder();

        if( dialogDataDto instanceof DialogDataDto ){
            return dialogDataDto;
        }

        return null;
    }

}