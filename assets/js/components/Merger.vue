<template>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <b-alert variant="success"
                             dismissible
                             :show="showSuccessAlert"
                             @dismissed="showSuccessAlert=false">
                        {{ successMessage }}
                    </b-alert>
                    <b-alert variant="danger"
                             dismissible
                             :show="showDangerAlert"
                             @dismissed="showDangerAlert=false">
                        {{ dangerMessage }}
                    </b-alert>
                    <div class="form-group">
                        <form class="form-inline">
                            <div class="form-group">
                                <input class="form-control" type="text" v-on:keyup="filter" v-model="filter" placeholder="Type here to filter" />
                                <button type="button" class="btn btn-success" v-on:click="loadState">Merge</button>
                            </div>
                        </form>
                    </div>
                    <table class="table table-bordered" id="entity-list">
                        <thead>
                            <tr>
                                <th v-for="field in fields">{{ field }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr :class="getStyleForStatus(entity)" v-for="entity in entities">
                                <td v-for="field in fields" v-on:click="toggle(entity)">{{ entity[field] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        name: 'merger',
        data: function () {
            return {
                filter: '',
                showSuccessAlert: false,
                successMessage: '',
                showDangerAlert: false,
                dangerMessage: '',
                entities: [],
                fields: [],
            }
        },
        methods: {
            getStyleForStatus: function (entity) {
                if (!('status' in entity)) {
                    return '';
                } else if (entity['status'] === 'source') {
                    return 'table-warning';
                } else if (entity['status'] === 'target') {
                    return 'table-success';
                }
                return '';
            },
            toggle: function(entity) {
                if (!('status' in entity)) {
                    entity['status'] = 'source';
                } else if (entity['status'] === 'source') {
                    entity['status'] = 'target';
                } else if (entity['status'] === 'target') {
                    entity.splice('status', 1);
                }
            },
            loadState: function () {
                axios.get('/data-sanitize/project/state').then((response) => {
                    this.entities = response.data.entities;
                    this.fields = response.data.fields;
                }, (error) => {
                    this.dangerMessage = 'Entities could not be loaded';
                    this.showDangerAlert = true;
                })
            },
            // sendTest: function () {
            //     axios.get('/cm-sms/message/send/' + this.phoneNumber).then((response) => {
            //         this.successMessage = 'Message sent to ' + this.phoneNumber;
            //         this.showSuccessAlert = true;
            //         this.loadState();
            //     }, (error) => {
            //         this.dangerMessage = 'Message could not be sent';
            //         this.showDangerAlert = true;
            //         this.loadState();
            //     })
            // }
        },
        mounted: function () {
            this.$nextTick(function () {
                this.loadState(this);
            });
        }
    }
</script>

<style>
    .fade {
        opacity: 1;
    }
</style>