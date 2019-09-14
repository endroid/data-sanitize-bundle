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
                                <button type="button" class="btn btn-success" v-on:click="merge">Merge</button>
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
                            <tr v-for="entity in entities" v-bind:class="{'table-warning':(status === 'source')}">
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
                fields: []
            }
        },
        props: {
            stateUrl: { required: true },
            mergeUrl: { required: true }
        },
        methods: {
            loadState: function () {
                axios.get(this.stateUrl).then((response) => {
                    this.entities = response.data.entities;
                    this.fields = response.data.fields;
                }, (error) => {
                    this.dangerMessage = 'Entities could not be loaded';
                    this.showDangerAlert = true;
                })
            },
            toggle: function(entity) {
                if (!('status' in entity) || entity['status'] === 'none') {
                    entity['status'] = 'source';
                } else if (entity['status'] === 'source') {
                    entity['status'] = 'target';
                } else if (entity['status'] === 'target') {
                    entity['status'] = 'none';
                }
            },
            merge: function() {
            	let sources = this.entities.filter(entity => entity.status === 'source').map(entity => entity['id']);
				let target = this.entities.filter(entity => entity.status === 'target').map(entity => entity['id']);
                axios.get(this.mergeUrl, { params: { sources: sources, target: target[0] }}).then((response) => {
                	this.successMessage = 'Merge successful';
                	this.showSuccessAlert = true;
                	this.loadState();
                }, (error) => {
                	this.dangerMessage = 'Merge failed';
                	this.showDangerAlert = true;
                	this.loadState();
                });
            }
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