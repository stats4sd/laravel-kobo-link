<template>
    <div>
        <b-table
            :items="teamForms"
            :fields="fields"
        >
            <template #cell(is_active)="row">
                <span v-if="!row.item.kobo_id">Undeployed</span>
                <span v-if="row.item.kobo_id && row.item.is_active == 1">Deployed - </span>
                <span v-if="row.item.kobo_id && row.item.is_active == 0 && !row.item.kobo_version_id">Uploaded - </span>
                <span v-if="row.item.kobo_id && row.item.is_active == 0 && row.item.kobo_version_id">Archived - </span>
                <a
                    v-if="row.item.kobo_id"
                    target="_blank"
                    :href="
                        'https://kf.kobotoolbox.org/#/forms/' +
                            row.item.kobo_id +
                            '/summary'
                    "
                >Show on KoBoToolbox</a>
            </template>
            <template #cell(actions)="row">
                <b-button-group>
                    <b-button
                        size="sm"
                        :variant="row.item.kobo_id ? 'info' : 'success'"
                        :disabled="row.item.processing == 1"
                        @click="deployForm(row.item)"
                    >
                        <span v-if="!row.item.kobo_id">Deploy Form</span>
                        <span v-if="row.item.kobo_id && row.item.is_active == 1">Update to latest form</span>
                        <span v-if="row.item.kobo_id && row.item.is_active == 0">Re-deploy form</span>
                    </b-button>
                    <b-button
                        size="sm"
                        variant="warning"
                        :disabled="row.item.processing == 1 || row.item.is_active == 0"
                        @click="archiveForm(row.item)"
                    >
                        Archive Form
                    </b-button>
                    <div v-if="row.item.processing == 1">
                        <span
                            class="spinner-border spinner-border-sm text-muted"
                        />
                    </div>
                </b-button-group>
            </template>
        </b-table>
        <b-button
            size="sm"
            variant="info"
            :disabled="teamForms.some(form => form.processing == 1)"
            @click="syncData()"
        >
            Pull Submissions From Kobo
        </b-button>
    </div>
</template>

<script>
    import axios from 'axios'

    export default {
        props: {
            team: {
                default: () => {},
                type: Object,
            },
            userId: {
                default: null,
                type: Number,
            },
        },
        data() {
            return {
                teamForms: [],
                fields: [
                    'title',
                    {
                        key: 'records',
                        label: 'Num. Submissions',
                    },
                    {
                        key: 'is_active',
                        label: 'Deployed?',
                    },
                    'actions',
                ],
            }
        },

        mounted() {
            this.getTeamForms();

            this.setupEchoListeners();
        },

        methods: {
            getTeamForms() {
                axios.get('/admin/team/' + this.team.id + '/xlsforms')
                    .then((response) => this.teamForms = response.data)
                    .catch((err) => alert(err))
            },

            deployForm(form){
                this.setProcessing(form);

                axios.post('/admin/teamxlsform/'+form.id+'/deploytokobo')
                    .then((response) => console.log(response))
                    .catch((err) => {
                        alert('form could not be deployed. Error: ' + err.response.responseText)
                        this.stopProcessing(form)
                    })
            },

            archiveForm(form){
                this.setProcessing(form);

                axios.post('/admin/teamxlsform/'+form.id+'/archive')
                    .then((response) => console.log(response))
                    .catch((err) => {
                        alert('form could not be archived. Error: ' + err.response.responseText)
                        this.stopProcessing(form)
                    })
            },

            // downloadData(form){
            //     axios.post('/admin/teamxlsform/'+form.id+'/downloadsubmissions')
            //         .then((response) => console.log(response))
            //         .catch((err) => {
            //             alert('form data could not be downloaded. Error: ' + err.response.responseText)
            //             this.stopProcessing(form)
            //         })
            // },

            syncData() {

                this.teamForms.forEach(form => {
                    this.setProcessing(form)
                    axios.post('/admin/teamxlsform/'+form.id+'/syncdata')
                        .then((response) => console.log(response))
                        .catch((err) => {
                            alert('form data could not be downloaded. Error: ' + err.response.responseText)
                            this.stopProcessing(form)
                        })
                })
            },

            setProcessing(form) {
                var index = this.teamForms.findIndex(teamForm => teamForm.id == form.id)
                this.teamForms[index].processing = 1
            },
            stopProcessing(form) {
                var index = this.teamForms.findIndex(teamForm => teamForm.id == form.id)
                this.teamForms[index].processing = 0
            },
            setupEchoListeners(){
                this.$echo
                    .private("App.Models.User." + this.userId)
                    .listen("KoboUploadReturnedSuccess", payload => {
                        new Noty({
                            type: "info",
                            text:
                                "<b>Form: " +
                                payload.form.title +
                                "</b><br/><br/>The form has been successfully uploaded to Kobotools. It will now be deployed and shared with the users of the team",
                            timeout: false
                        }).show();
                    })
                    .listen("KoboUploadReturnedError", payload => {
                        this.teamForms = this.teamForms.map(teamForm => {
                            if (teamForm.id === payload.form.id) {
                                return payload.form;
                            }
                            return teamForm;
                        });

                        new Noty({
                            type: "error",
                            text:
                                "<b>Form: " +
                                payload.form.title +
                                "</b><br/><br/> The form could not be deployed to Kobotools. An error was returned<hr/>Error Type: <b>" +
                                payload.errorType +
                                "</b><hr/>Error Message: <b>" +
                                payload.errorMessage +
                                "</b><br/><br/>This error may indicate errors in the XLSX form.",
                            timeout: false
                        }).show();
                    })
                    .listen("KoboDeploymentReturnedSuccess", payload => {
                        this.teamForms = this.teamForms.map(teamForm => {
                            if (teamForm.id === payload.form.id) {
                                return payload.form;
                            }
                            return teamForm;
                        });

                        new Noty({
                            type: "success",
                            text:
                                "<b>Form: " +
                                payload.form.title +
                                "</b><br/><br/>The form has been successfully uploaded and deployed to Kobotools.",
                            timeout: false
                        }).show();
                    })
                    .listen("KoboDeploymentReturnedError", payload => {
                        new Noty({
                            type: "error",
                            text:
                                "<b>Form: " +
                                payload.form.title +
                                "</b><br/><br/> The form could not be deployed to Kobotools. An error was returned<hr/>Error Type: <b>" +
                                payload.errorType +
                                "</b><hr/>Error Message: <b>" +
                                payload.errorMessage +
                                "</b><br/><br/>This error may indicate errors in the XLSX form.",
                            timeout: false
                        }).show();
                    })
                    .listen("KoboGetDataReturnedSuccess", payload => {
                        console.log(payload);
                        var text = "new submissions have "
                        if(payload.count == 1) text = "new submission has"

                        new Noty({
                            type: "success",
                            text:
                                "<b>Form: " +
                                payload.form.title +
                                "</b><br/><br/>" + payload.count + " " + text + " been successfully pulled from KoBoToolBox. The total number of submissions is shown in the table.",
                            timeout: false
                        }).show();
                        this.teamForms = this.teamForms.map(teamForm => {
                            if (teamForm.id === payload.form.id) {
                                return payload.form;
                            }
                            return teamForm;
                        });

                    })
                    .listen("KoboArchiveRequestReturnedSuccess", payload => {
                        new Noty({
                            type: "success",
                            text:
                                "<b>Form: " +
                                payload.form.title +
                                "</b><br/><br/>The form has been successfully archived on Kobotools, and is no longer available for data collection",
                            timeout: false
                        }).show();

                        this.teamForms = this.teamForms.map(teamForm => {
                            if (teamForm.id === payload.form.id) {
                                return payload.form;
                            }
                            return teamForm;
                        });
                    })
                    .listen("KoboArchiveRequestReturnedError", payload => {
                        new Noty({
                            type: "error",
                            text:
                                "<b>Form: " +
                                payload.form.title +
                                "</b><br/><br/> The form could not be archived. An error was returned<hr/>Error Type: <b>" +
                                payload.errorType +
                                "</b><hr/>Error Message: <b>" +
                                payload.errorMessage +
                                "</b>",
                            timeout: false
                        }).show();
                    });
            }
        }





    }
</script>