<?php
/**
 * Created by PhpStorm.
 * User: erick
 * Date: 12/22/2022
 * Time: 1:12 PM
 */

namespace App\Libraries;

class Dialog
{
    public static function renderDelete($buttonName='Delete', $title='DELETE', $formId='formDelete'){
      return <<< HTML
      <!-- <div class="modal fade dialog{$formId}" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">{$title}</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <h5 id="message{$formId}">Message</h5>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-secondary pull-left" data-dismiss="modal">Cancel</button>
                          <button id="btnDelete{$formId}" type="button" class="btn btn-primary btn-newformsubmit full-right">{$buttonName}</button>
                      </div>
                </div>
              </div>
          </div>
      </div> -->

      <div class="modal fade dialog{$formId}" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-dialog-popout" role="document">
              <div class="modal-content">
                  <div class="block block-themed block-transparent mb-0">
                      <div class="block-header bg-primary-dark">
                          <h3 class="block-title" id="exampleModalLabel">{$title}</h3>
                          <div class="block-options">
                              <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                  <i class="si si-close"></i>
                              </button>
                          </div>
                      </div>
                      <div class="block-content">
                          <p id="message{$formId}">Message</p>
                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Cancel</button>
                      <button id="btnDelete{$formId}" type="button" class="btn btn-alt-danger">
                          <i class="fa fa-trash"></i> {$buttonName}
                      </button>
                  </div>
              </div>
          </div>
      </div>
      HTML;
    }
}