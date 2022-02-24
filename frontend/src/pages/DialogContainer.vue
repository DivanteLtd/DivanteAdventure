<template>
  <div id="dialog-container">
    <project-window/>
    <add-criteria/>
    <employee-window/>
    <project-form/>
    <f-a-q-category-form/>
    <add-employee-project/>
    <edit-employee-project/>
    <delete-project-form/>
    <delete-tribe-form/>
    <tribe-window/>
    <request-window/>
    <request-form-window/>
    <attachment-form/>
    <agreement-form/>
    <agreement-attachment-form/>
    <period-window/>
    <evidence-dialog/>
    <post-view/>
    <post-edit-view/>
    <position-window/>
    <strategy-window/>
    <tribe-assign-dialog/>
    <position-assign-dialog/>
    <select-tribe-and-position-dialog/>
    <pin-dialog/>
    <link-edit-view/>
    <potential-employee-editor/>
    <slack-dialog/>
    <project-criteria/>
    <checklist-details-window/>
    <checklist-templates-window/>
    <question-update-dialog/>
    <hardware-form-dialog/>
    <password-dialog/>
    <add-contract-type/>
    <add-contract/>
  </div>
</template>

<script>
  import ProjectWindow from './firm/ProjectWindow';
  import ProjectCriteria from '../components/projects/window/ProjectCriteria';
  import AddCriteria from '../components/projects/window/AddCriteria';
  import EmployeeWindow from './firm/EmployeeWindow';
  import ProjectForm from '../components/projects/ProjectForm';
  import AddEmployeeProject from '../components/projects/AddEmployeeProject';
  import EditEmployeeProject from '../components/projects/EditEmployeeProject';
  import DeleteProjectForm from '../components/projects/DeleteProjectForm';
  import DeleteTribeForm from '../components/tribes/DeleteTribeForm';
  import AttachmentForm from '../components/agreements/AttachmentForm';
  import FAQCategoryForm from '../components/faq/FAQCategoryForm';
  import AgreementForm from '../components/agreements/AgreementForm';
  import AgreementAttachmentForm from '../components/agreements/AgreementAttachmentForm';
  import TribeWindow from './firm/TribeWindow';
  import RequestWindow from '../components/freeDays/requests/RequestWindow';
  import RequestFormWindow from '../components/freeDays/requestForm/RequestFormWindow';
  import PeriodWindow from '../components/freeDays/period/PeriodWindow';
  import EvidenceDialog from '../components/evidences/window/EvidenceDialog';
  import PostView from '../components/dashboard/PostView';
  import PostEditView from '../components/dashboard/PostEditView';
  import PositionWindow from '../components/positions/positions/PositionWindow';
  import StrategyWindow from '../components/positions/strategies/StrategyWindow';
  import TribeAssignDialog from '../components/tribes/dialog/TribeAssignDialog';
  import PositionAssignDialog from '../components/tribes/dialog/PositionAssignDialog';
  import SelectTribeAndPositionDialog from '../components/employees/window/SelectTribeAndPositionDialog';
  import PinDialog from './PinDialog';
  import LinkEditView from '../components/dashboard/LinkEditView';
  import PotentialEmployeeEditor from './hr/PotentialEmployeeEditor';
  import SlackDialog from './SlackDialog';
  import ChecklistDetailsWindow from './checklist/ChecklistDetailsWindow';
  import ChecklistTemplatesWindow from '../components/hr/checklistTemplates/checklistTemplatesWindow/Stepper';
  import QuestionUpdateDialog from './question/QuestionUpdateDialog';
  import HardwareFormDialog from '../components/hardware/HardwareDialog';
  import PasswordDialog from '../components/hardware/PasswordDialog';
  import AddContractType from '../components/dictionaries/window/AddContractType';
  import AddContract from '../components/employees/window/AddContract';

  export default {
    name: 'DialogContainer',
    components: {
      AddContract,
      AddContractType,
      PasswordDialog,
      ChecklistDetailsWindow,
      SlackDialog,
      PotentialEmployeeEditor,
      AddCriteria,
      PinDialog,
      FAQCategoryForm,
      SelectTribeAndPositionDialog,
      PositionAssignDialog,
      TribeAssignDialog,
      StrategyWindow,
      PositionWindow,
      PostEditView,
      PostView,
      ProjectWindow,
      EmployeeWindow,
      TribeWindow,
      ProjectForm,
      AddEmployeeProject,
      EditEmployeeProject,
      DeleteProjectForm,
      DeleteTribeForm,
      RequestWindow,
      RequestFormWindow,
      AttachmentForm,
      AgreementForm,
      AgreementAttachmentForm,
      PeriodWindow,
      EvidenceDialog,
      LinkEditView,
      ProjectCriteria,
      ChecklistTemplatesWindow,
      QuestionUpdateDialog,
      HardwareFormDialog,
    },
  };
</script>
